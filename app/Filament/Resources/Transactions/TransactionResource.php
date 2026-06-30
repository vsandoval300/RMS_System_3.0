<?php

namespace App\Filament\Resources\Transactions;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\View;
use App\Services\TransactionLogsPreviewService;
use Filament\Tables\Contracts\HasTable;
use stdClass;
use Filament\Tables\Grouping\Group;
use Filament\Actions\ActionGroup;
use Filament\Actions\ViewAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Resources\Transactions\RelationManagers\LogsRelationManager;
use App\Filament\Resources\Transactions\RelationManagers\SupportsRelationManager;
use App\Filament\Resources\Transactions\Pages\ListTransactions;
use App\Filament\Resources\Transactions\Pages\CreateTransaction;
use App\Filament\Resources\Transactions\Pages\ViewTransaction;
use App\Filament\Resources\Transactions\Pages\EditTransaction;
use App\Filament\Resources\TransactionResource\Pages;
use App\Filament\Resources\TransactionResource\RelationManagers;
use App\Models\Transaction;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Illuminate\Support\Str;
use Filament\Forms\Components\Placeholder;
use App\Models\TransactionType;
use Filament\Support\RawJs;
use App\Models\OperativeDoc;
use Filament\Tables\Filters\SelectFilter;
use Filament\Infolists\Components\TextEntry;
use Filament\Forms\Components\Hidden;
use App\Models\TransactionLog;
use App\Models\Reinsurer;
use App\Models\TransactionStatus;
use Filament\Actions\Action;
use App\Models\CostScheme;
use Illuminate\Support\HtmlString;
use Filament\Tables\Columns\ViewColumn;
use Filament\Infolists\Components\ViewEntry;


use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Grid;


use Filament\Infolists\Components\RepeatableEntry;
use Illuminate\Support\Facades\Storage;





class TransactionResource extends Resource
{
    protected static ?string $model = Transaction::class;

    protected static ?string $navigationLabel  = 'Instalments';
    protected static ?string $modelLabel       = 'Instalment';
    protected static ?string $pluralModelLabel = 'Instalments';

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-minus';
    protected static string | \UnitEnum | null $navigationGroup = 'Transactions';
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
            ->select('transactions.*')
            ->orderBy('reinsurers.name')
            ->orderBy('transactions.op_document_id')
            ->orderBy('transactions.index');
    }

    /*--------------------------------------------------------------
    | 1. Form
    --------------------------------------------------------------*/
    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([

                Hidden::make('prefill_op_document_id')
                    ->default(fn () => request()->query('op_document_id'))
                    ->dehydrated(false)
                    ->visible(false)
                    ->afterStateHydrated(function ($state, Get $get, Set $set, ?Transaction $record) {
                        if ($record?->exists || blank($state)) {
                            return;
                        }

                        if (blank($get('op_document_id'))) {
                            $set('op_document_id', $state);
                        }

                        static::applyDocumentDefaults($get('op_document_id'), $get, $set);

                        if (filled($get('installment_number'))) {
                            static::buildTransactionsBatch(
                                $get('op_document_id'),
                                (int) $get('installment_number'),
                                $set
                            );
                        }
                    }),

                Hidden::make('preview_logs')
                    ->default([])
                    ->dehydrated(false),

                Section::make('Instalment Settings')
                    ->description(fn (string $operation) => match ($operation) {
                        'edit' => 'Review the transaction details and update only the transaction lifecycle records as needed.',
                        default => 'Select the document and specify the number of installments to be created.',
                    })
                    ->columns(8)
                    ->columnSpanFull()
                    ->schema([

                        Select::make('op_document_id')
                            ->label('Document')
                            ->placeholder('Select document.')
                            //->helperText('Choose the document to be used for transaction generation.')
                            ->relationship('operativeDoc', 'id')
                            ->searchable()
                            ->disabled(fn (?Transaction $record) => $record?->exists)
                            ->dehydrated()
                            ->preload()
                            ->optionsLimit(10000)
                            ->required()
                            ->live()
                            ->columnSpan(2)
                            ->default(fn () => request()->query('op_document_id'))
                            ->afterStateHydrated(function ($state, Get $get, Set $set, ?Transaction $record) {
                                if ($record?->exists || blank($state)) {
                                    return;
                                }

                                static::applyDocumentDefaults($state, $get, $set);

                                if (filled($get('installment_number'))) {
                                    static::buildTransactionsBatch(
                                        $state,
                                        (int) $get('installment_number'),
                                        $set
                                    );
                                }
                            })
                            ->afterStateUpdated(function ($state, Set $set, Get $get, ?Transaction $record) {
                                if ($record?->exists) {
                                    return;
                                }

                                $set('transactions_batch', []);

                                static::applyDocumentDefaults($state, $get, $set);

                                if (filled($state)) {
                                    static::buildTransactionsBatch(
                                        $state,
                                        (int) ($get('installment_number') ?: 1),
                                        $set
                                    );
                                }
                            }),


                        TextInput::make('transaction_status_display')
                            ->label('Transaction Status')
                            ->visibleOn('edit')
                            ->readOnly()
                            ->dehydrated(false)
                            ->formatStateUsing(fn (?Transaction $record) =>
                                $record?->fresh('status')?->status?->transaction_status ?? 'Pending'
                            )
                            ->suffixIcon(fn (?Transaction $record) => match (
                                $record?->fresh('status')?->status?->transaction_status
                            ) {
                                'Completed' => 'heroicon-m-check-circle',
                                'In process' => 'heroicon-m-clock',
                                default => 'heroicon-m-exclamation-circle',
                            })
                            ->columnSpan(1),


                        DatePicker::make('due_date')
                            ->label('Due Date')
                            ->visibleOn('edit')
                            ->dehydrated(fn (string $operation) => $operation === 'edit')
                            ->columnSpan(1),

                        TextInput::make('exch_rate')
                            ->label('Exchange Rate')
                            ->visibleOn('edit')
                            ->numeric()
                            ->dehydrated(fn (string $operation) => $operation === 'edit')
                            ->columnSpan(1),

                        TextInput::make('proportion_display')
                            ->label('Proportion')
                            ->visibleOn('edit')
                            ->readOnly()
                            ->dehydrated(false)
                            ->suffix('%')
                            ->formatStateUsing(fn (?Transaction $record) =>
                                $record?->proportion !== null
                                    ? number_format(((float) $record->proportion) * 100, 2)
                                    : null
                            )
                            ->columnSpan(1),


                        View::make('filament.components.transaction-progress-bar')
                            ->visibleOn('edit')
                            ->viewData(fn (?Transaction $record) => [
                                'progress' => $record?->fresh()?->lifecycleProgressPercentage() ?? 0,
                            ])
                            ->columnSpan(2),


                        TextInput::make('installment_number')
                            ->label('Instalments')
                            ->visibleOn('create')
                            //->helperText('Number of transactions to generate.')
                            ->default(1)
                            ->required()
                            ->readOnly()
                            ->inputMode('numeric')
                            ->rule('integer')
                            ->minValue(1)
                            ->extraInputAttributes([
                                'style' => 'text-align:center;',
                            ])

                            ->prefixAction(
                                Action::make('minus')
                                    ->icon('heroicon-m-minus')
                                    ->action(function (Get $get, Set $set) {
                                        $current = (int) ($get('installment_number') ?: 1);
                                        $newValue = max(1, $current - 1);

                                        $set('installment_number', $newValue);

                                        $docId = $get('op_document_id');

                                        if (blank($docId)) {
                                            $set('transactions_batch', []);
                                            return;
                                        }

                                        static::buildTransactionsBatch(
                                            $docId,
                                            $newValue,
                                            $set
                                        );
                                    })
                            )

                            ->suffixAction(
                                Action::make('plus')
                                    ->icon('heroicon-m-plus')
                                    ->action(function (Get $get, Set $set) {
                                        $current = (int) ($get('installment_number') ?: 0);
                                        $newValue = $current + 1;

                                        $set('installment_number', $newValue);

                                        $docId = $get('op_document_id');

                                        if (blank($docId)) {
                                            $set('transactions_batch', []);
                                            return;
                                        }

                                        static::buildTransactionsBatch(
                                            $docId,
                                            $newValue,
                                            $set
                                        );
                                    })
                            )

                            ->placeholder('Enter installment number')
                            ->dehydrated(false)
                            ->live(debounce: 200)

                            ->afterStateUpdated(function ($state, Get $get, Set $set) {
                                $docId = $get('op_document_id');

                                if (
                                    blank($docId) ||
                                    blank($state) ||
                                    (int) $state <= 0
                                ) {
                                    $set('transactions_batch', []);
                                    return;
                                }

                                static::buildTransactionsBatch(
                                    $docId,
                                    (int) $state,
                                    $set
                                );
                            })

                            ->columnSpan(1),
                    ]),



                Repeater::make('transactions_batch')
                     ->label(
                        new HtmlString(
                            '<span style="padding-left:18px; font-size:16px; font-weight:600;">
                                Instalments Batch
                            </span>'
                        )
                    )
                    ->columnSpanFull()
                    ->dehydrated()
                    ->visible(fn (Get $get) =>
                        filled($get('op_document_id')) &&
                        filled($get('installment_number')) &&
                        (int) $get('installment_number') > 0
                    )
                    ->addable(false)
                    ->deletable(false)
                    ->reorderable(false)
                    ->schema([

                        Section::make('Transaction Information')
                            ->description("Overview of the transaction's primary details.")
                            ->columns(8)
                            ->schema([

                                TextInput::make('index')
                                    ->required()
                                    ->numeric()
                                    ->disabled()
                                    ->dehydrated()
                                    ->columnSpan(1),

                                TextInput::make('id')
                                    ->label('Id transaction')
                                    ->disabled()
                                    ->dehydrated()
                                    ->columnSpan(2),

                                Hidden::make('transaction_type_id')
                                    ->default(1),

                                Hidden::make('preview_logs')
                                    ->default([])
                                    ->dehydrated(false),    

                                DatePicker::make('due_date')
                                    ->label('Due Date')
                                    ->required()
                                    ->live()
                                    ->afterStateUpdated(fn (Get $get, Set $set) => static::recalcBatchPreviewLogs($get, $set))
                                    ->columnSpan(1),

                                TextInput::make('proportion')
                                    ->label('Proportion')
                                    
                                    ->dehydrated()
                                    ->default('')
                                    ->placeholder('Enter proportion')
                                    ->suffix('%')
                                    ->required()
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn (Get $get, Set $set) => static::recalcBatchPreviewLogs($get, $set))
                                    ->inputMode('decimal')
                                    ->rule('numeric')
                                    ->minValue(0)
                                    ->maxValue(100)
                                    ->step(0.01)
                                    
                                    ->columnSpan(1),

                                TextInput::make('exch_rate')
                                    ->label('Exchange Rate')
                                    ->dehydrated()
                                    ->placeholder('Enter exchange rate')
                                    ->required()
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn (Get $get, Set $set) => static::recalcBatchPreviewLogs($get, $set))
                                    ->numeric()
                                    ->disabled(fn (Get $get) => (bool) $get('exchange_rate_locked'))
                                    ->minValue(0)
                                    ->step(0.00001)
                                    ->columnSpan(1),

                                Select::make('cost_scheme_option')
                                    ->label('Cost Scheme')
                                    ->placeholder('Select cost scheme.')
                                    ->native(false)
                                    ->live()
                                    ->afterStateUpdated(fn (Get $get, Set $set) => static::recalcBatchPreviewLogs($get, $set))
                                    ->options(function (Get $get) {
                                        $docId = $get('../../op_document_id');

                                        if (blank($docId)) {
                                            return [];
                                        }

                                        $schemes = CostScheme::query()
                                            ->whereHas('businessDocSchemes', function ($query) use ($docId) {
                                                $query->where('op_document_id', $docId);
                                            })
                                            ->pluck('id', 'id')
                                            ->toArray();

                                        return count($schemes) > 1
                                            ? ['all' => 'All'] + $schemes
                                            : $schemes;
                                    })
                                    ->columnSpan(2),

                                Hidden::make('exchange_rate_locked')
                                    ->default(false)
                                    ->dehydrated(false),

                                Section::make('Transaction Lifecycle')
                                    ->columnSpanFull()
                                    ->description('Preview of generated lifecycle records based on current form values.')
                                    ->schema([
                                        View::make('filament.resources.transaction.transaction-logs-preview')
                                            ->viewData(fn (Get $get) => [
                                                'logs' => $get('preview_logs') ?? [],
                                            ])
                                            ->dehydrated(false),
                                    ])
                                    ->collapsed(false),




                            ]),

                    ]),
            ]);
    }




protected static function buildTransactionsBatch(string $opDocumentId, int $count, Set $set): void
{
    $baseIndex = Transaction::where('op_document_id', $opDocumentId)->count();

    $opDoc = OperativeDoc::query()
        ->with('business')
        ->whereKey($opDocumentId)
        ->first();

    $currencyId = $opDoc?->business?->currency_id;

    $exchRate = ((int) $currencyId === 157) ? 1 : ($opDoc?->roe_fs ?? null);
    $exchangeRateLocked = ((int) $currencyId === 157);

    $schemes = CostScheme::query()
        ->whereHas('businessDocSchemes', function ($query) use ($opDocumentId) {
            $query->where('op_document_id', $opDocumentId);
        })
        ->pluck('id')
        ->toArray();

    $costSchemeOption = count($schemes) === 1
        ? $schemes[0]
        : 'all';

    $rows = [];

    for ($i = 1; $i <= $count; $i++) {
        $rows[] = [
            'index' => $baseIndex + $i,
            'id' => (string) Str::uuid(),
            'transaction_type_id' => 1,
            'due_date' => null,
            'proportion' => '',
            'exch_rate' => $exchRate,
            'exchange_rate_locked' => $exchangeRateLocked,
            'cost_scheme_option' => $costSchemeOption,
            'preview_logs' => [],
        ];
    }

    $set('transactions_batch', $rows);
}

protected static function recalcBatchPreviewLogs(Get $get, Set $set): void
{
    $opDocumentId = $get('../../op_document_id');

    $proportionUi = $get('proportion');
    $exchRate     = $get('exch_rate');
    $dueDate      = $get('due_date');
    $costSchemeId = $get('cost_scheme_option');

    if (
        blank($opDocumentId) ||
        $proportionUi === '' ||
        $proportionUi === null ||
        blank($exchRate) ||
        blank($dueDate) ||
        blank($costSchemeId)
    ) {
        $set('preview_logs', []);
        return;
    }

    $proportionDecimal = ((float) str_replace([',', '%'], '', (string) $proportionUi)) / 100;

    $logs = app(TransactionLogsPreviewService::class)->build(
        opDocumentId: (string) $opDocumentId,
        typeId: 1,
        proportion: (float) $proportionDecimal,
        exchRate: (float) $exchRate,
        remittanceCode: null,
        dueDate: $dueDate,

        // ✅ NUEVO
        costSchemeId: $costSchemeId === 'all' ? null : (string) $costSchemeId,
    );

    $set('preview_logs', $logs);
}


// ✅✅✅ [NEW]
protected static function recalcPreviewLogs(Get $get, Set $set): void
{
    $opDocumentId = $get('op_document_id');

    // ✅ Siempre será 1
    //$typeId = $get('transaction_type_id') ?: 1;

    $proportionUi = $get('proportion');
    $exchRate     = $get('exch_rate');
    $dueDate      = $get('due_date');

    if (
        blank($opDocumentId) ||
        $proportionUi === '' ||
        $proportionUi === null ||
        blank($exchRate)
    ) {
        $set('preview_logs', []);
        return;
    }

    if ($dueDate === '' || $dueDate === null) {
        $set('preview_logs', []);
        return;
    }

    $proportionDecimal = ((float) str_replace([',', '%'], '', (string) $proportionUi)) / 100;

    $logs = app(TransactionLogsPreviewService::class)->build(
        opDocumentId: (string) $opDocumentId,
        typeId: 1,
        proportion: (float) $proportionDecimal,
        exchRate: (float) $exchRate,
        remittanceCode: $get('remmitance_code'),
        dueDate: $get('due_date'),
    );

    $set('transaction_type_id', 1);
    $set('preview_logs', $logs);
}



protected static function applyDocumentDefaults(?string $opDocumentId, Get $get, Set $set): void
{
    if (blank($opDocumentId)) {
        $set('index', null);
        $set('transaction_type_id', 1);
        $set('due_date', null);
        $set('exch_rate', null);
        $set('exchange_rate_locked', false);
        $set('proportion', '');
        $set('cost_scheme_option', null);
        $set('preview_logs', []);

        return;
    }

    // ✅ No limpiar transaction_type_id; siempre debe quedarse en 1
    $set('transaction_type_id', 1);
    $set('due_date', null);
    $set('proportion', '');
    $set('preview_logs', []);

    $nextIndex = Transaction::where('op_document_id', $opDocumentId)->count() + 1;
    $set('index', $nextIndex);

    if (blank($get('id'))) {
        $set('id', (string) Str::uuid());
    }

    // ✅ Cost schemes asociados al documento
    $schemes = CostScheme::query()
        ->whereHas('businessDocSchemes', function ($query) use ($opDocumentId) {
            $query->where('op_document_id', $opDocumentId);
        })
        ->pluck('id')
        ->toArray();

    $set(
        'cost_scheme_option',
        count($schemes) === 1
            ? $schemes[0]
            : 'all'
    );

    $opDoc = OperativeDoc::query()
        ->with('business')
        ->whereKey($opDocumentId)
        ->first();

    $currencyId = $opDoc?->business?->currency_id;
    $currentRate = $get('exch_rate');

    if ((int) $currencyId === 157) {
        $set('exch_rate', 1);
        $set('exchange_rate_locked', true);
    } else {
        $set('exchange_rate_locked', false);

        if ($currentRate === null || $currentRate === '' || (float) $currentRate === 1.0) {
            $set('exch_rate', $opDoc?->roe_fs ?? null);
        }
    }

    static::recalcPreviewLogs($get, $set);
}




/*--------------------------------------------------------------
 | 2. Infolist
 --------------------------------------------------------------*/
public static function infolist(Schema $schema): Schema
{
    return $schema->components([
        Section::make('Transaction Profile')
            ->columnSpanFull()
            ->schema([
                \Filament\Schemas\Components\Grid::make()
                    ->columns([
                        'default' => 1,
                        'md' => 2,
                    ])
                    ->extraAttributes(['style' => 'column-gap:24px;'])
                    ->schema([

                        /* ───────────── LEFT COLUMN ───────────── */
                        \Filament\Schemas\Components\Grid::make(1)
                            ->columnSpan([
                                'default' => 1,
                                'md' => 1,
                            ])
                            ->extraAttributes(['style' => 'row-gap:0;'])
                            ->schema([

                                \Filament\Schemas\Components\Grid::make(12)
                                    ->extraAttributes([
                                        'style' => 'border-bottom:1px solid rgba(255,255,255,0.12); padding:2px 0;',
                                    ])
                                    ->schema([
                                        TextEntry::make('id_label')->hiddenLabel()
                                            ->state('Id transaction:')
                                            ->weight('bold')
                                            ->alignment('right')
                                            ->columnSpan(3),

                                        TextEntry::make('id')->hiddenLabel()
                                            ->state(fn ($record) => $record->id ?: '—')
                                            ->columnSpan(9),
                                    ]),

                                \Filament\Schemas\Components\Grid::make(12)
                                    ->extraAttributes([
                                        'style' => 'border-bottom:1px solid rgba(255,255,255,0.12); padding:2px 0;',
                                    ])
                                    ->schema([
                                        TextEntry::make('index_label')->hiddenLabel()
                                            ->state('Index:')
                                            ->weight('bold')
                                            ->alignment('right')
                                            ->columnSpan(3),

                                        TextEntry::make('index')->hiddenLabel()
                                            ->state(fn ($record) => $record->index ?: '—')
                                            ->columnSpan(9),
                                    ]),

                                \Filament\Schemas\Components\Grid::make(12)
                                    ->extraAttributes([
                                        'style' => 'border-bottom:1px solid rgba(255,255,255,0.12); padding:2px 0;',
                                    ])
                                    ->schema([
                                        TextEntry::make('document_label')->hiddenLabel()
                                            ->state('Document:')
                                            ->weight('bold')
                                            ->alignment('right')
                                            ->columnSpan(3),

                                        TextEntry::make('op_document_id')->hiddenLabel()
                                            ->state(fn ($record) => $record->op_document_id ?: '—')
                                            ->columnSpan(9),
                                    ]),

                                \Filament\Schemas\Components\Grid::make(12)
                                    ->extraAttributes([
                                        'style' => 'border-bottom:1px solid rgba(255,255,255,0.12); padding:2px 0;',
                                    ])
                                    ->schema([
                                        TextEntry::make('type_label')->hiddenLabel()
                                            ->state('Transaction type:')
                                            ->weight('bold')
                                            ->alignment('right')
                                            ->columnSpan(3),

                                        TextEntry::make('type.description')->hiddenLabel()
                                            ->state(fn ($record) => $record->type?->description ?? '—')
                                            ->columnSpan(9),
                                    ]),
                            ]),

                        /* ───────────── RIGHT COLUMN ───────────── */
                        \Filament\Schemas\Components\Grid::make(1)
                            ->columnSpan([
                                'default' => 1,
                                'md' => 1,
                            ])
                            ->extraAttributes(['style' => 'row-gap:0;'])
                            ->schema([

                                \Filament\Schemas\Components\Grid::make(12)
                                    ->extraAttributes([
                                        'style' => 'border-bottom:1px solid rgba(255,255,255,0.12); padding:2px 0;',
                                    ])
                                    ->schema([
                                        TextEntry::make('status_label')->hiddenLabel()
                                            ->state('Transaction status:')
                                            ->weight('bold')
                                            ->alignment('right')
                                            ->columnSpan(4),

                                        TextEntry::make('status.transaction_status')->hiddenLabel()
                                            ->state(fn ($record) => $record->status?->transaction_status ?? '—')
                                            ->columnSpan(8),
                                    ]),

                                \Filament\Schemas\Components\Grid::make(12)
                                    ->extraAttributes([
                                        'style' => 'border-bottom:1px solid rgba(255,255,255,0.12); padding:2px 0;',
                                    ])
                                    ->schema([
                                        TextEntry::make('remmitance_label')->hiddenLabel()
                                            ->state('Remittance code:')
                                            ->weight('bold')
                                            ->alignment('right')
                                            ->columnSpan(4),

                                        TextEntry::make('remmitanceCode.remmitance_code')->hiddenLabel()
                                            ->state(fn ($record) => $record->remmitanceCode?->remmitance_code ?? '—')
                                            ->columnSpan(8),
                                    ]),

                                \Filament\Schemas\Components\Grid::make(12)
                                    ->extraAttributes([
                                        'style' => 'border-bottom:1px solid rgba(255,255,255,0.12); padding:2px 0;',
                                    ])
                                    ->schema([
                                        TextEntry::make('duedate_label')->hiddenLabel()
                                            ->state('Due date:')
                                            ->weight('bold')
                                            ->alignment('right')
                                            ->columnSpan(4),

                                        TextEntry::make('due_date')->hiddenLabel()
                                            ->state(fn ($record) =>
                                                $record->due_date
                                                    ? $record->due_date->format('M j, Y')
                                                    : '—'
                                            )
                                            ->columnSpan(8),
                                    ]),

                                \Filament\Schemas\Components\Grid::make(12)
                                    ->extraAttributes([
                                        'style' => 'border-bottom:1px solid rgba(255,255,255,0.12); padding:2px 0;',
                                    ])
                                    ->schema([
                                        TextEntry::make('proportion_label')->hiddenLabel()
                                            ->state('Proportion:')
                                            ->weight('bold')
                                            ->alignment('right')
                                            ->columnSpan(4),

                                        TextEntry::make('proportion')->hiddenLabel()
                                            ->state(fn ($record) =>
                                                $record->proportion !== null
                                                    ? number_format(((float) $record->proportion) * 100, 2) . '%'
                                                    : '—'
                                            )
                                            ->columnSpan(8),
                                    ]),

                                






                                    
                            ]),

                            
                    ]),

                    \Filament\Schemas\Components\Grid::make(12)
                    ->extraAttributes([
                        'style' => '
                            border-bottom:1px solid rgba(255,255,255,0.12);
                            padding:12px 0;
                        ',
                    ])
                    ->schema([

                        TextEntry::make('lifecycle_progress_label')
                            ->hiddenLabel()
                            ->state('Lifecycle progress:')
                            ->weight('bold')
                            ->alignment('right')
                            ->columnSpan(2),

                        ViewEntry::make('lifecycle_progress')
                            ->hiddenLabel()
                            ->view('filament.components.transaction-progress-bar-inline')
                            ->viewData(fn ($record) => [
                                'progress' => $record?->fresh()?->lifecycleProgressPercentage() ?? 0,
                            ])
                            ->columnSpan(5),

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
                    ->state(function (HasTable $livewire, stdClass $rowLoop): int {
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

                TextColumn::make('operativeDoc.business.reinsurer.short_name')
                    ->label('Reinsurer')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('op_document_id')
                    ->label('Document')
                    ->sortable(query: fn (Builder $query, string $direction): Builder =>
                        $query->orderBy('transactions.op_document_id', $direction)
                              ->orderBy('transactions.index')
                    )
                    ->searchable(),

                TextColumn::make('index')
                    ->label('Index')
                    ->sortable()
                    ->formatStateUsing(function ($state, Transaction $record): string {
                        static $countCache = [];
                        $docId = $record->op_document_id;
                        if (! isset($countCache[$docId])) {
                            $countCache[$docId] = Transaction::where('op_document_id', $docId)
                                ->whereNull('deleted_at')
                                ->count();
                        }
                        return "{$state} / {$countCache[$docId]}";
                    }),

                TextColumn::make('proportion')
                    ->label('Proportion')
                    ->formatStateUsing(fn ($state) => $state !== null ? number_format($state * 100, 2) . '%' : null)
                    ->sortable(),

                TextColumn::make('exch_rate')
                    ->numeric(decimalPlaces: 5)
                    ->sortable(),

                TextColumn::make('due_date')
                    ->date()
                    ->sortable()
                    ->color(fn (Transaction $record): ?string =>
                        $record->due_date?->isPast() &&
                        $record->status?->transaction_status !== 'Completed'
                            ? 'danger'
                            : null
                    )
                    ->icon(fn (Transaction $record): ?string =>
                        $record->due_date?->isPast() &&
                        $record->status?->transaction_status !== 'Completed'
                            ? 'heroicon-m-exclamation-triangle'
                            : null
                    )
                    ->iconColor('danger')
                    ->description(fn (Transaction $record): ?string =>
                        $record->due_date && $record->status?->transaction_status !== 'Completed'
                            ? (function () use ($record) {
                                $days = (int) now()->startOfDay()->diffInDays($record->due_date->startOfDay(), false);
                                return $days >= 0 ? "+{$days} days" : "{$days} days";
                            })()
                            : null
                    ),

                TextColumn::make('remmitance_code')
                    ->label('Remittance')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('type.description')
                    ->label('Type')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('latest_net_amount')
                    ->label('Net amount')
                    ->numeric(decimalPlaces: 2)
                    ->alignEnd()
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

                ViewColumn::make('lifecycle_progress')
                    ->label('Progress')
                    ->view('filament.components.transaction-progress-column')
                    ->sortable(false),

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
                Group::make('operativeDoc.business.reinsurer.name')
                    ->label('Reinsurer'),
            ])

            ->filters([
                SelectFilter::make('reinsurer_id')
                    ->label('Reinsurer')
                    ->options(
                        Reinsurer::query()
                            ->orderBy('short_name')
                            ->pluck('short_name', 'id')
                    )
                    ->searchable()
                    ->preload()
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['value'] ?? null,
                            fn (Builder $query, $value): Builder =>
                                $query->whereHas('operativeDoc.business', function (Builder $query) use ($value) {
                                    $query->where('reinsurer_id', $value);
                                })
                        );
                    }),

                SelectFilter::make('transaction_status_id')
                    ->label('Status')
                    ->options(
                        TransactionStatus::query()
                            ->orderBy('transaction_status')
                            ->pluck('transaction_status', 'id')
                    )
                    ->searchable()
                    ->preload(),

                SelectFilter::make('transaction_type_id')
                    ->label('Type')
                    ->options(
                        TransactionType::query()
                            ->orderBy('description')
                            ->pluck('description', 'id')
                    )
                    ->searchable()
                    ->preload(),

                SelectFilter::make('op_document_id')
                    ->label('Document')
                    ->options(
                        OperativeDoc::query()
                            ->orderBy('id')
                            ->pluck('id', 'id')
                    )
                    ->searchable()
                    ->preload(),
            ])
            
            ->recordActions([
                ActionGroup::make([
                    Action::make('lifecycle')
                        ->label('Lifecycle')
                        ->icon('heroicon-o-list-bullet')
                        ->modalHeading(fn (Transaction $record) =>
                            "Transaction Lifecycle - {$record->op_document_id}"
                        )
                        ->modalWidth('7xl')
                        ->modalSubmitAction(false)
                        ->modalCancelActionLabel('Close')
                        ->schema([
                            \Filament\Schemas\Components\View::make(
                                'filament.components.transaction-logs-table'
                            )
                                ->viewData(fn (Transaction $record) => [
                                    'logs' => $record->logs()
                                        ->with([
                                            'deduction',
                                            'fromPartner',
                                            'toPartner',
                                        ])
                                        ->orderBy('index')
                                        ->get(),
                                ]),
                        ]),
                    ViewAction::make(),
                    EditAction::make(),
                    DeleteAction::make(),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            LogsRelationManager::class,
            SupportsRelationManager::class,
            \App\Filament\Resources\Transactions\RelationManagers\RecalculationsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTransactions::route('/'),
            'create' => CreateTransaction::route('/create'),
            'view'   => ViewTransaction::route('/{record}'),   // 👈 NUEVA
            'edit' => EditTransaction::route('/{record}/edit'),
        ];
    }
}
