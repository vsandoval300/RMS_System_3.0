<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransactionResource\Pages;
use App\Filament\Resources\TransactionResource\RelationManagers;
use App\Models\Transaction;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Section;
use Illuminate\Support\Str;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Components\Placeholder;
use App\Models\TransactionType;
use Filament\Support\RawJs;
use App\Models\OperativeDoc;
use Filament\Tables\Filters\SelectFilter;

use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Grid as InfoGrid;
use Filament\Infolists\Components\Section as InfoSection;
use Filament\Infolists\Components\TextEntry;
use Filament\Forms\Components\View;
use Filament\Forms\Components\Hidden;
use App\Models\TransactionLog;

use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Grid;


use Filament\Infolists\Components\RepeatableEntry;
use Illuminate\Support\Facades\Storage;





class TransactionResource extends Resource
{
    protected static ?string $model = Transaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-minus';
    protected static ?string $navigationGroup = 'Transactions';
    protected static ?int    $navigationSort  = 1;   // aparecerá primero

    /* ───── NUEVO: burbuja con el total en el menú ───── */
    public static function getNavigationBadge(): ?string
    {
        // Puedes usar self::$model::count() o Reinsurer::count()
        return Transaction::count();
    }
   


   public static function getTableQuery(): Builder
    {
        return parent::getTableQuery()
            ->join('operative_docs', 'transactions.op_document_id', '=', 'operative_docs.id')
            ->join('businesses', 'operative_docs.business_id', '=', 'businesses.id')
            ->join('reinsurers', 'businesses.reinsurer_id', '=', 'reinsurers.id')
            ->select('transactions.*') // 👈 importante para evitar conflictos de columnas
            ->orderBy('reinsurers.name')
            ->orderBy('transactions.op_document_id');
    }

/*--------------------------------------------------------------
 | 1. Form
 --------------------------------------------------------------*/
public static function form(Form $form): Form
{
    return $form
        ->schema([

            Hidden::make('prefill_op_document_id')
                ->default(fn () => request()->query('op_document_id'))
                ->dehydrated(false)
                ->visible(false)
                ->afterStateHydrated(function ($state, Get $get, Set $set, ?Transaction $record) {
                    // Solo en create
                    if ($record?->exists) {
                        return;
                    }

                    // Si no viene por URL, no hacemos nada
                    if (blank($state)) {
                        return;
                    }

                    // Si el select aún no tiene valor, lo seteamos
                    if (blank($get('op_document_id'))) {
                        $set('op_document_id', $state);
                    }

                    // Ejecutar la misma lógica que cuando el usuario selecciona el documento
                    static::applyDocumentDefaults($get('op_document_id'), $get, $set);
                }),

            // ✅✅✅ [NEW] Estado para guardar el preview (NO se guarda en DB)
            Hidden::make('preview_logs')
                ->default([])
                ->dehydrated(false),

            Section::make('Transaction Information')
                ->description("Overview of the transaction's primary details.")
                ->schema([

                    Section::make()
                        ->columns(8)
                        ->schema([
                            // ───── Columna 1: Document ─────
                            Select::make('op_document_id')
                                ->label('Document')
                                ->placeholder('Select document.')
                                ->relationship('operativeDoc', 'id')
                                ->searchable()
                                ->preload()
                                ->optionsLimit(10000)
                                ->required()
                                ->live()
                                ->columnSpan(2)
                                ->default(fn () => request()->query('op_document_id'))

                                // ✅ Cuando viene precargado, simula que el usuario lo seleccionó
                                ->afterStateHydrated(function (Select $component, $state, ?Transaction $record) {
                                    if ($record?->exists) {
                                        return;
                                    }

                                    if (blank($state)) {
                                        return;
                                    }

                                    // 🔥 Esto dispara tu afterStateUpdated y por ende applyDocumentDefaults()
                                    $component->callAfterStateUpdated();
                                })

                                // ✅ Cuando el usuario selecciona manualmente
                                ->afterStateUpdated(function ($state, Set $set, Get $get, ?Transaction $record) {
                                    if ($record?->exists) return;
                                    static::applyDocumentDefaults($state, $get, $set);
                                }),





                            // ───── Columna 2: Vacía ─────
                            Placeholder::make('spacer')
                                ->label(' ')
                                ->content(' ')
                                ->columnSpan(3),

                            // ───── Columna 3: Index ─────
                            TextInput::make('index')
                                ->required()
                                ->numeric()
                                ->disabled()
                                ->dehydrated()
                                ->columnSpan(1)
                                ->afterStateHydrated(function ($state, Get $get, Set $set, ?Transaction $record) {
                                    if ($record?->exists) return;

                                    // Solo si viene documento precargado y el index está vacío
                                    $docId = $get('op_document_id');
                                    if (blank($state) && filled($docId)) {
                                        $set('index', Transaction::where('op_document_id', $docId)->count() + 1);
                                    }
                                }),

                                                        // ───── Columna 4: Id ─────
                                                        TextInput::make('id')
                                                            ->label('Id transaction')
                                                            ->disabled()
                                                            ->dehydrated()
                                                            ->columnSpan(2)
                                                            ->afterStateHydrated(function ($state, Get $get, Set $set, ?Transaction $record) {
                                    if ($record?->exists) return;

                                    $docId = $get('op_document_id');
                                    if (blank($state) && filled($docId)) {
                                        $set('id', (string) Str::uuid());
                                    }
                                }),
                        ]),

                    Section::make()
                        ->columns(4)
                        ->schema([

                            Select::make('transaction_type_id')
                                ->label('Type')
                                ->placeholder('Select a transaction type')
                                ->options(
                                    TransactionType::query()
                                        ->orderBy('id')
                                        ->get()
                                        ->mapWithKeys(fn ($type) => [
                                            $type->id => "{$type->id} - {$type->description}",
                                        ])
                                        ->toArray()
                                )
                                ->searchable()
                                ->required()
                                ->live() // ✅✅✅ [NEW] para disparar preview
                                ->afterStateUpdated(fn (Get $get, Set $set) => static::recalcPreviewLogs($get, $set)) // ✅✅✅ [NEW]
                                ->columnSpan(1),

                            Select::make('transaction_status_id')
                                ->label('Status')
                                ->relationship('status', 'transaction_status')
                                ->required()
                                ->default(fn (?Transaction $record) => $record ? null : 1)
                                ->disabled(fn (?Transaction $record) => $record === null)
                                ->dehydrated()
                                ->columnSpan(1),

                            DatePicker::make('due_date')
                                ->label('Due Date')
                                ->required()
                                //->native(false) // opcional pero ayuda en muchos casos
                                ->live()        // 👈 importante
                                ->afterStateUpdated(fn (Get $get, Set $set) => static::recalcPreviewLogs($get, $set))
                                ->columnSpan(1),

                            Select::make('remmitance_code')
                                ->label('Remittance code')
                                ->placeholder('Select an option')
                                ->relationship('remmitanceCode', 'remmitance_code')
                                ->searchable()
                                ->preload()
                                ->native(false)
                                ->live() // ✅✅✅ [NEW] si afecta preview
                                ->afterStateUpdated(fn (Get $get, Set $set) => static::recalcPreviewLogs($get, $set)) // ✅✅✅ [NEW]
                                ->columnSpan(1),
                        ]),

                    Section::make()
                        ->columns(4)
                        ->schema([

                            TextInput::make('proportion')
                                ->label('Proportion')
                                ->visibleOn('create')   // 👈 solo create
                                ->dehydrated()
                                ->placeholder('Enter proportion (0%–100%).')
                                ->suffix('%')
                                ->required()
                                ->numeric()
                                ->live(onBlur: true)
                                ->afterStateUpdated(fn (Get $get, Set $set) => static::recalcPreviewLogs($get, $set)) // ✅✅✅ [NEW]
                                ->minValue(0)
                                ->maxValue(100)
                                ->step(0.01)
                                ->mask(RawJs::make('$money($input, ".", ",", 2)'))
                                ->formatStateUsing(fn ($state) => $state !== null ? round(((float) $state) * 100, 2) : null)
                                ->dehydrateStateUsing(function ($state) {
                                    if ($state === null || $state === '') {
                                        return null;
                                    }

                                    $value = (float) str_replace(',', '', (string) $state);

                                    return $value / 100;
                                })
                                ->columnSpan(1),

                            TextInput::make('exch_rate')
                                ->label('Exchange Rate')
                                ->visibleOn('create')   // 👈 solo create
                                ->dehydrated()
                                ->placeholder('Enter the transaction exchange rate.')
                                ->required()
                                ->numeric()
                                ->live(onBlur: true)
                                ->afterStateUpdated(fn (Get $get, Set $set) => static::recalcPreviewLogs($get, $set)) // ✅✅✅ [NEW]
                                ->minValue(0)
                                ->step(0.00001)
                                ->columnSpan(1),

                            // ───── Columna 2: Vacía ─────
                            Placeholder::make('spacer')
                                ->label(' ')
                                ->content(' ')
                                ->columnSpan(1),

                            // ───── Columna 2: Vacía ─────
                            Placeholder::make('spacer')
                                ->label(' ')
                                ->content(' ')
                                ->columnSpan(1),

                           
                        ]),
                ])
                ->columns(2),


             

            // ✅✅✅ [NEW] SECCIÓN CON TABLA PREVIEW (readonly)
            Section::make('Transaction Lifecycle')
                ->description('Preview of generated lifecycle records based on current form values.')
                ->visibleOn('create')
                ->schema([
                    View::make('filament.resources.transaction.transaction-logs-preview')
                        ->viewData(fn (Get $get) => [
                            'logs' => $get('preview_logs') ?? [],
                        ])
                        ->dehydrated(false),
                ])
                ->collapsed(false),

        ]);
}






// ✅✅✅ [NEW]
protected static function recalcPreviewLogs(Get $get, Set $set): void
{
    $opDocumentId = $get('op_document_id');
    $typeId       = $get('transaction_type_id');
    $proportionUi = $get('proportion');   // UI: 0–100
    $exchRate     = $get('exch_rate');
    $dueDate      = $get('due_date');

    // Si falta lo mínimo, no hay preview
    if (blank($opDocumentId) || blank($typeId) || blank($proportionUi) || blank($exchRate)) {
        $set('preview_logs', []);
        return;
    }

    // si por algún motivo viene como string vacío en un re-render, no recalcules
    if ($dueDate === '' || $dueDate === null) {
        $set('preview_logs', []);
        return;
    }

    $proportionDecimal = ((float) str_replace(',', '', (string) $proportionUi)) / 100;

    $logs = app(\App\Services\TransactionLogsPreviewService::class)->build(
        opDocumentId: (string) $opDocumentId,
        typeId: (int) $typeId,
        proportion: (float) $proportionDecimal,
        exchRate: (float) $exchRate,
        remittanceCode: $get('remmitance_code'),
        dueDate: $get('due_date'),
    );

    $set('preview_logs', $logs);
}



protected static function applyDocumentDefaults(?string $opDocumentId, Get $get, Set $set): void
{
    if (blank($opDocumentId)) {
        $set('index', null);
        $set('id', null);
        $set('exch_rate', null);
        $set('preview_logs', []);
        return;
    }

    // ✅ Index
    $nextIndex = Transaction::where('op_document_id', $opDocumentId)->count() + 1;
    $set('index', $nextIndex);

    // ✅ Id transaction
    $set('id', (string) Str::uuid());

    // ✅ currency rule
    $currencyId = OperativeDoc::query()
        ->whereKey($opDocumentId)
        ->with('business:business_code,currency_id')
        ->first()
        ?->business
        ?->currency_id;

    $currentRate = $get('exch_rate');

    if ((int) $currencyId === 157) {
        $set('exch_rate', 1);
    } else {
        if ($currentRate === null || $currentRate === '' || (float) $currentRate === 1.0) {
            $set('exch_rate', null);
        }
    }

    // ✅ preview
    static::recalcPreviewLogs($get, $set);
}





/*--------------------------------------------------------------
 | 2. Infolist
 --------------------------------------------------------------*/
public static function infolist(Infolist $infolist): Infolist
{
    return $infolist->schema([
        InfoSection::make('Transaction Profile')
            ->schema([
                // ✅ Grid padre con 2 columnas (en pantallas medianas+)
                InfoGrid::make()
                    ->columns([
                        'default' => 1,
                        'md' => 2,
                    ])
                    ->extraAttributes(['style' => 'column-gap:24px;'])
                    ->schema([

                        /* ───────────── LEFT COLUMN ───────────── */
                        InfoGrid::make(1)
                            ->columnSpan([
                                'default' => 1,
                                'md' => 1,   // ✅ clave: NO ocupar las 2 columnas
                            ])
                            ->extraAttributes(['style' => 'row-gap:0;'])
                            ->schema([

                                // Id
                                InfoGrid::make(12)->extraAttributes([
                                    'style' => 'border-bottom:1px solid rgba(255,255,255,0.12); padding:2px 0;',
                                ])->schema([
                                    TextEntry::make('id_label')->label('')
                                        ->state('Id transaction:')
                                        ->weight('bold')->alignment('right')->columnSpan(3),
                                    TextEntry::make('id')->label('')
                                        ->state(fn ($record) => $record->id ?: '—')
                                        ->columnSpan(9),
                                ]),

                                // Index
                                InfoGrid::make(12)->extraAttributes([
                                    'style' => 'border-bottom:1px solid rgba(255,255,255,0.12); padding:2px 0;',
                                ])->schema([
                                    TextEntry::make('index_label')->label('')
                                        ->state('Index:')
                                        ->weight('bold')->alignment('right')->columnSpan(3),
                                    TextEntry::make('index')->label('')
                                        ->state(fn ($record) => $record->index ?: '—')
                                        ->columnSpan(9),
                                ]),

                                // Document
                                InfoGrid::make(12)->extraAttributes([
                                    'style' => 'border-bottom:1px solid rgba(255,255,255,0.12); padding:2px 0;',
                                ])->schema([
                                    TextEntry::make('document_label')->label('')
                                        ->state('Document:')
                                        ->weight('bold')->alignment('right')->columnSpan(3),
                                    TextEntry::make('op_document_id')->label('')
                                        ->state(fn ($record) => $record->op_document_id ?: '—')
                                        ->columnSpan(9),
                                ]),

                                // Transaction type
                                InfoGrid::make(12)->extraAttributes([
                                    'style' => 'border-bottom:1px solid rgba(255,255,255,0.12); padding:2px 0;',
                                ])->schema([
                                    TextEntry::make('type_label')->label('')
                                        ->state('Transaction type:')
                                        ->weight('bold')->alignment('right')->columnSpan(3),
                                    TextEntry::make('type.description')->label('')
                                        ->state(fn ($record) => $record->type?->description ?? '—')
                                        ->columnSpan(9),
                                ]),

                                // Transaction type
                                /* InfoGrid::make(12)->extraAttributes([
                                    'style' => 'border-bottom:1px solid rgba(255,255,255,0.12); padding:2px 0;',
                                ])->schema([
                                    TextEntry::make('amount')
                                        ->label('')
                                        ->state('Amount:')
                                        ->weight('bold')
                                        ->alignment('right')
                                        ->columnSpan(3),
                                    TextEntry::make('amount')
                                        ->label('')
                                        ->state(fn ($record) =>
                                            filled($record->amount)
                                                ? number_format((float) $record->amount, 2, '.', ',')
                                                : '—'
                                        )
                                        ->columnSpan(9),
                                ]), */
                            ]),

                        /* ───────────── RIGHT COLUMN ───────────── */
                        InfoGrid::make(1)
                            ->columnSpan([
                                'default' => 1,
                                'md' => 1,   // ✅ clave: NO ocupar las 2 columnas
                            ])
                            ->extraAttributes(['style' => 'row-gap:0;'])
                            ->schema([

                                // Transaction status
                                InfoGrid::make(12)->extraAttributes([
                                    'style' => 'border-bottom:1px solid rgba(255,255,255,0.12); padding:2px 0;',
                                ])->schema([
                                    TextEntry::make('status_label')->label('')
                                        ->state('Transaction status:')
                                        ->weight('bold')->alignment('right')->columnSpan(3),
                                    TextEntry::make('status.transaction_status')->label('')
                                        ->state(fn ($record) => $record->status?->transaction_status ?? '—')
                                        ->columnSpan(9),
                                ]),

                                // Remittance code
                                InfoGrid::make(12)->extraAttributes([
                                    'style' => 'border-bottom:1px solid rgba(255,255,255,0.12); padding:2px 0;',
                                ])->schema([
                                    TextEntry::make('remmitance_label')->label('')
                                        ->state('Remittance code:')
                                        ->weight('bold')->alignment('right')->columnSpan(3),
                                    TextEntry::make('remmitanceCode.remmitance_code')->label('')
                                        ->state(fn ($record) => $record->remmitanceCode?->remmitance_code ?? '—')
                                        ->columnSpan(9),
                                ]),

                                // Due date
                                InfoGrid::make(12)->extraAttributes([
                                    'style' => 'border-bottom:1px solid rgba(255,255,255,0.12); padding:2px 0;',
                                ])->schema([
                                    TextEntry::make('duedate_label')->label('')
                                        ->state('Due date:')
                                        ->weight('bold')->alignment('right')->columnSpan(3),
                                    TextEntry::make('due_date')->label('')
                                        ->state(fn ($record) =>
                                            $record->due_date
                                                ? $record->due_date->format('M j, Y')
                                                : '—'
                                        )
                                        ->columnSpan(9),
                                ]),

                                // Proportion
                                InfoGrid::make(12)->extraAttributes([
                                    'style' => 'border-bottom:1px solid rgba(255,255,255,0.12); padding:2px 0;',
                                ])->schema([
                                    TextEntry::make('proportion_label')->label('')
                                        ->state('Porportion:')
                                        ->weight('bold')->alignment('right')->columnSpan(3),
                                    TextEntry::make('proportion')->label('')
                                        ->state(fn ($record) =>
                                            $record->proportion !== null
                                                ? number_format(((float) $record->proportion) * 100, 2) . '%'
                                                : '—'
                                        )
                                        ->columnSpan(9),
                                ]),
                            ]),
                    ]),
            ])
            ->maxWidth('8xl')
            ->collapsible(),

    ]);
}













    /*--------------------------------------------------------------
     | 3. Table
    --------------------------------------------------------------*/
    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query
                ->with([
                    'type',
                    'status',
                    'operativeDoc.business.reinsurer',
                    'remmitanceCode',
                ])
                ->addSelect([
                    'latest_net_amount' => TransactionLog::query()
                        ->select('net_amount')
                        ->whereColumn('transaction_logs.transaction_id', 'transactions.id')
                        ->orderByDesc('index')
                        ->limit(1),
                ])
            )
            ->columns([

                TextColumn::make('row_number')
                    ->label('#')
                    ->alignCenter()
                    ->state(function (\Filament\Tables\Contracts\HasTable $livewire, \stdClass $rowLoop): int {
                        $perPage = (int) ($livewire->getTableRecordsPerPage() ?? 0);
                        $page    = (int) ($livewire->getTablePage() ?? 1);

                        // Si por alguna razón perPage llega como 0, evita cálculos raros:
                        if ($perPage <= 0) {
                            return (int) $rowLoop->iteration;
                        }

                        return (($page - 1) * $perPage) + (int) $rowLoop->iteration;
                    }),

                TextColumn::make('id')
                    ->label('Id transaction')
                    ->copyable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('operativeDoc.business.reinsurer.name')
                    ->label('Reinsurer')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('op_document_id')
                    ->label('Document')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('index')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('proportion')
                    ->label('Proportion')
                    ->formatStateUsing(fn ($state) => $state !== null ? number_format($state * 100, 2) . '%' : null)
                    ->sortable(),

                TextColumn::make('exch_rate')
                    ->numeric(decimalPlaces: 5)
                    ->sortable(),

                TextColumn::make('due_date')
                    ->date()
                    ->sortable(),

                TextColumn::make('remmitance_code')
                    ->label('Remittance')
                    ->searchable(),

                TextColumn::make('type.description')
                    ->label('Type')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('latest_net_amount')
                    ->label('Net amount')
                    ->numeric(decimalPlaces: 2)
                    ->sortable(),     

                TextColumn::make('status.transaction_status')
                    ->label('Status')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'Pending'   => 'gray',
                        'In process'   => 'warning',
                        'Completed'  => 'success',
                        default     => 'secondary',
                    })
                    ->searchable()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('operative_docs.business_id') 
            ->defaultSort('transactions.op_document_id')
            ->groups([
                Tables\Grouping\Group::make('operativeDoc.business.reinsurer.name')
                    ->label('Reinsurer'),
            ])

            ->filters([
                SelectFilter::make('op_document_id')
                    ->label('Document')
                    ->relationship('operativeDoc', 'id') // ✅ AJUSTA: nombre de relación en Transaction
                    ->searchable()
                    ->preload(),
            ])
            
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\LogsRelationManager::class,
            RelationManagers\SupportsRelationManager::class,
            
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTransactions::route('/'),
            'create' => Pages\CreateTransaction::route('/create'),
            'view'   => Pages\ViewTransaction::route('/{record}'),   // 👈 NUEVA
            'edit' => Pages\EditTransaction::route('/{record}/edit'),
        ];
    }
}
