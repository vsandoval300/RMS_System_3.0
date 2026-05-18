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
use Filament\Tables\Columns\IconColumn;
use Filament\Forms\Components\FileUpload;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Facades\Storage;   
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Illuminate\Support\Str;



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
                    DatePicker::make('sent_date'),
                    DatePicker::make('received_date'),
                ]),

            Section::make()
                ->columnSpanFull()
                ->columns(2)
                ->schema([
                    TextInput::make('exch_rate')->numeric(),
                    TextInput::make('status')
                        ->maxLength(30)
                        ->disabled()
                        ->dehydrated(false),
                ]),

            Section::make()
                ->columnSpanFull()
                ->columns(4)
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
                        ->dehydrateStateUsing(fn ($state) =>
                            $state === null || $state === '' ? null : (float) str_replace(',', '', (string) $state)
                        ),

                    // ✅ net_amount es storedAs(...) → no editable
                    TextInput::make('net_amount')
                        ->label('Net amount')
                        ->disabled()
                        ->dehydrated(false),

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
                TextColumn::make('exch_rate')->numeric(decimalPlaces: 5),
                TextColumn::make('gross_amount')->numeric(2),
                TextColumn::make('gross_amount_calc')->numeric(2),
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
                EditAction::make(),
                
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
