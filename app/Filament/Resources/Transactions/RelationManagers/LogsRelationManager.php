<?php

namespace App\Filament\Resources\Transactions\RelationManagers;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Illuminate\Filesystem\FilesystemAdapter;
use Filament\Forms;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Support\RawJs;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Tables\Columns\IconColumn;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Illuminate\Support\HtmlString;
use App\Models\TransactionLog;
use Illuminate\Support\Facades\Storage;   
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Illuminate\Support\Str;
use Livewire\Attributes\On;



class LogsRelationManager extends RelationManager
{
    protected static string $relationship = 'logs';
    protected static ?string $title = 'Transaction Lifecycle';

    // ✅ Bloquea crear logs manualmente
    public function canCreate(): bool
    {
        return false;
    }

    // ✅ Bloquea borrar (si quieres)
    public function canDelete($record): bool
    {
        return false;
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make()
                ->columnSpanFull()
                ->columns(6)
                ->schema([
                    TextInput::make('index')
                        ->numeric()
                        ->disabled()            // ✅ siempre
                        ->dehydrated(false)
                        ->columnSpan(1),

                    TextInput::make('transaction_id')
                        ->label('Transaction Id')
                        ->disabled()            // ✅ siempre
                        ->dehydrated(false)
                        ->columnSpan(3),

                    Select::make('deduction_type')
                        ->label('Deduction type')
                        ->relationship('deduction', 'concept')
                        ->searchable()
                        ->preload()
                        ->disabled()
                        ->dehydrated(false)
                        ->columnSpan(2),

                    Select::make('from_entity')
                        ->label('From entity')
                        ->relationship('fromPartner', 'short_name')
                        ->searchable()
                        ->preload()
                        ->disabled()
                        ->dehydrated(false)
                        ->columnSpan(3),

                    Select::make('to_entity')
                        ->label('To entity')
                        ->relationship('toPartner', 'short_name')
                        ->searchable()
                        ->preload()
                        ->disabled()
                        ->dehydrated(false)
                        ->columnSpan(3),
                ]),

            Section::make()
                ->columnSpanFull()
                ->columns(2)
                ->schema([
                    Hidden::make('prev_received_date')->dehydrated(false),

                    DatePicker::make('sent_date')
                        ->live()
                        ->minDate(fn (Get $get) => $get('prev_received_date') ?: null)
                        ->rules([
                            fn (Get $get): \Closure => function (string $attribute, mixed $value, \Closure $fail) use ($get) {
                                $prevReceived = $get('prev_received_date');
                                if (filled($prevReceived) && filled($value) && $value < $prevReceived) {
                                    $fail('Sent date must be on or after the previous log\'s Received date.');
                                }
                            },
                        ]),

                    DatePicker::make('received_date')
                        ->minDate(fn (Get $get) => $get('sent_date') ?: null)
                        ->rules([
                            fn (Get $get): \Closure => function (string $attribute, mixed $value, \Closure $fail) use ($get) {
                                $sent = $get('sent_date');
                                if (filled($sent) && filled($value) && $value < $sent) {
                                    $fail('Received date must be on or after Sent date.');
                                }
                            },
                        ]),
                ]),

            Section::make()
                ->columnSpanFull()
                ->columns(2)
                ->schema([
                    TextInput::make('exch_rate')->numeric()->readOnly()->dehydrated(false),
                    TextInput::make('status')
                        ->maxLength(30)
                        ->disabled()
                        ->dehydrated(false),
                ]),

            Section::make()
                ->columnSpanFull()
                ->columns(5)
                ->schema([
                    TextInput::make('gross_amount')
                        ->label('Gross amount')
                        ->required()
                        ->mask(RawJs::make('$money($input, ".", ",", 2)'))
                        ->dehydrateStateUsing(fn ($state) =>
                            $state === null || $state === '' ? null : (float) str_replace(',', '', (string) $state)
                        ),

                    TextInput::make('gross_amount_calc')
                        ->label('Gross amount calc')
                        ->disabled()
                        ->dehydrated(false),

                    TextInput::make('commission_discount')
                        ->label('Commission discount')
                        ->disabled()
                        ->dehydrated(false),

                    TextInput::make('banking_fee')
                        ->label('Banking fee')
                        ->required()
                        ->mask(RawJs::make('$money($input, ".", ",", 2)'))
                        ->live(onBlur: true)
                        ->afterStateUpdated(function ($state, Get $get, Set $set) {
                            $gross    = (float) str_replace(',', '', (string) ($get('gross_amount_calc') ?? 0));
                            $discount = (float) str_replace(',', '', (string) ($get('commission_discount') ?? 0));
                            $fee      = (float) str_replace(',', '', (string) ($state ?? 0));
                            $set('net_amount', number_format(round($gross - $discount - $fee, 2), 2, '.', ','));
                        })
                        ->dehydrateStateUsing(fn ($state) =>
                            $state === null || $state === '' ? null : (float) str_replace(',', '', (string) $state)
                        ),

                    TextInput::make('net_amount')
                        ->label('Net amount')
                        ->disabled()
                        ->dehydrated(false)
                        ->mask(RawJs::make('$money($input, ".", ",", 2)')),

            // ✅ NUEVA SECCIÓN: Evidence
            Section::make('Evidence')
                ->columnSpanFull()
                ->description('Upload a PDF evidence file for this log row (optional).')
                ->schema([
                    FileUpload::make('evidence_path')
                        ->label('Evidence (PDF)')
                        ->disk('s3')
                        ->directory('reinsurers/transactions/log_evidence')
                        ->visibility('public') // o 'private'
                        ->openable()
                        ->downloadable()
                        ->acceptedFileTypes([
                            'application/pdf',
                        ])
                        ->maxSize(20480) // 20MB
                        ->helperText('Only PDF files are allowed.')
                        ->getUploadedFileNameForStorageUsing(function (TemporaryUploadedFile $file, $record) {
                            // En edición: usa el id real del log
                            // En create (si aún no existe record): genera uno temporal
                            $id = $record?->id ?: (string) Str::uuid();

                            // Como ya es PDF, forzamos extensión .pdf
                            return "{$id}.pdf";
                        }),
                ]),


            ]),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('index')
                    ->label('#')
                    ->sortable(),

                TextColumn::make('deduction.concept')
                    ->label('Deduction')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('fromPartner.short_name')
                    ->label('Source')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('toPartner.short_name')
                    ->label('Destination')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('sent_date')->date(),
                TextColumn::make('received_date')->date(),
                //TextColumn::make('gross_amount')->numeric(2),
                TextColumn::make('gross_amount_calc')->label('Premium Fts')->numeric(2),
                TextColumn::make('commission_discount')->label('Discount')->numeric(2),
                TextColumn::make('banking_fee')->numeric(2),
                TextColumn::make('net_amount')->numeric(2),

                // ✅ NUEVA COLUMNA: icono de evidencia (después de net_amount)
                // 👉 Nombre del archivo (solo texto)
                IconColumn::make('evidence_path')
                    ->label('File')
                    // 👇 Convertimos el state a boolean (hay archivo o no)
                    ->boolean()
                    ->state(fn ($record) => filled($record->evidence_path))
                    // ✅ Icono cuando SÍ hay archivo
                    ->trueIcon('heroicon-o-document-text')
                    // ✅ Icono cuando NO hay archivo
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('primary')
                    ->falseColor('gray')
                    ->tooltip(fn ($record) =>
                        filled($record->evidence_path)
                            ? 'View document'
                            : 'No document available'
                    )
                    ->alignCenter()
                    ->action(
                        Action::make('viewEvidence')
                            ->hidden(fn ($record) => blank($record->evidence_path))
                            ->modalHeading(fn ($record) => "Evidence – {$record->id}")
                            ->modalWidth('7xl')
                            ->modalSubmitAction(false)
                            ->modalContent(function ($record) {
                                $path = $record->evidence_path;

                                if (blank($path)) {
                                    return new HtmlString('<p>No document available.</p>');
                                }

                                /** @var FilesystemAdapter $disk */
                                $disk = Storage::disk('s3');

                                $key = filter_var($path, FILTER_VALIDATE_URL)
                                    ? ltrim(parse_url($path)['path'] ?? '', '/')
                                    : $path;

                                if (! $disk->exists($key)) {
                                    return new HtmlString(
                                        '<p>The file does not exist in S3.</p>
                                        <p><code>' . e($key) . '</code></p>'
                                    );
                                }

                                $url = method_exists($disk, 'temporaryUrl')
                                    ? $disk->temporaryUrl(
                                        $key,
                                        now()->addMinutes(10),
                                        [
                                            'ResponseContentType'        => 'application/pdf',
                                            'ResponseContentDisposition' => 'inline; filename="' . basename($key) . '"',
                                        ]
                                    )
                                    : $disk->url($key);

                                return view('filament.components.pdf-viewer', [
                                    'url' => $url,
                                ]);
                            })
                    ),

                TextColumn::make('status')->badge(),
            ])
            ->defaultSort('index', 'asc')
            ->headerActions([
                // ✅ NO CreateAction
            ])
            ->recordActions([
                EditAction::make()
                    ->mutateRecordDataUsing(function (array $data, $record): array {
                        $prevLog = TransactionLog::query()
                            ->where('transaction_id', $record->transaction_id)
                            ->where('index', $record->index - 1)
                            ->withoutTrashed()
                            ->first();

                        $data['prev_received_date'] = $prevLog?->received_date
                            ? \Carbon\Carbon::parse($prevLog->received_date)->format('Y-m-d')
                            : null;

                        return $data;
                    })
                    ->modalWidth('7xl')
                    ->after(function ($record, $livewire) {
                        $record->refresh();

                        $logStatus = filled($record->received_date)
                            ? 'Completed'
                            : (filled($record->sent_date) ? 'In process' : 'Pending');

                        $record->forceFill([
                            'status' => $logStatus,
                        ])->saveQuietly();

                        $transaction = $livewire->getOwnerRecord()->refresh();

                        $logs = $transaction->logs()
                            ->withoutTrashed()
                            ->orderBy('index')
                            ->get();

                        $statuses = $logs
                            ->pluck('status')
                            ->map(fn ($status) => trim((string) $status));

                        $lastLog = $logs->last();

                        $transactionStatusId = match (true) {
                            $lastLog && trim((string) $lastLog->status) === 'Completed' => 3,
                            $statuses->contains('In process') => 2,
                            $statuses->contains('Completed') => 2,
                            default => 1,
                        };

                        $transaction->forceFill([
                            'transaction_status_id' => $transactionStatusId,
                        ])->saveQuietly();

                        $livewire->dispatch('transaction-status-updated');
                        $livewire->dispatch('$refresh');
                    }),
                
                        /* Action::make('viewPdf')
                            ->label('View PDF')
                            ->hidden(fn ($record) => blank($record->support_path))
                            ->modalHeading(fn ($record) => "PDF – {$record->id}")
                            ->modalWidth('7xl')
                            ->modalSubmitAction(false)
                            ->modalContent(function ($record) {
                                $path = $record->support_path;

                                if (blank($path)) {
                                    return new HtmlString('<p>No document available.</p>');
                                }

                                /** @var \Illuminate\Filesystem\FilesystemAdapter $disk 
                                $disk = Storage::disk('s3');

                                // Si viene una URL completa, intentamos recuperar solo la "key" del objeto
                                if (filter_var($path, FILTER_VALIDATE_URL)) {
                                    // Ejemplo simple: quitar dominio de S3 y quedarnos con la key
                                    $parsed = parse_url($path);
                                    $key = ltrim($parsed['path'] ?? '', '/');
                                } else {
                                    $key = $path;
                                }

                                if (! $disk->exists($key)) {
                                    return new HtmlString(
                                        '<p>The PDF file does not exist in S3.</p>'
                                        .'<p><code>' . e($key) . '</code></p>'
                                    );
                                }

                                // 🔥 Siempre generamos una URL temporal con headers "inline"
                                $url = $disk->temporaryUrl(
                                    $key,
                                    now()->addMinutes(10),
                                    [
                                        'ResponseContentType'        => 'application/pdf',
                                        'ResponseContentDisposition' => 'inline; filename="'.basename($key).'"',
                                    ]
                                );

                                return view('filament.components.pdf-viewer', [
                                    'url' => $url,
                                ]);
                            }) */
                    
            ])
            ->toolbarActions([
                // ✅ NO DeleteBulkAction
            ]);
    }
}
