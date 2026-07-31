<?php

namespace App\Filament\Resources\PaymentFlow;

use App\Models\Transaction;
use App\Models\Reinsurer;
use App\Models\TransactionStatus;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ViewColumn;
use Filament\Tables\Columns\ColumnGroup;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\DatePicker;
use Filament\Actions\Action;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use App\Filament\Resources\PaymentFlow\Pages;

class PaymentFlowResource extends Resource
{
    protected static ?string $model = Transaction::class;

    protected static ?string $navigationLabel  = 'Premium Payment Summary';
    protected static ?string $modelLabel       = 'Premium Payment Summary';
    protected static ?string $pluralModelLabel = 'Premium Payment Summary';
    protected static ?string $slug             = 'payment-flow';

    protected static string|\BackedEnum|null $navigationIcon  = 'heroicon-o-document-chart-bar';
    protected static string|\UnitEnum|null   $navigationGroup = 'Transactions';
    protected static ?int                    $navigationSort  = 2;

    // ── Read-only resource ──────────────────────────────────────────────────
    public static function canCreate(): bool                      { return false; }
    public static function canEdit(Model $record): bool           { return false; }
    public static function canDelete(Model $record): bool         { return false; }
    public static function canDeleteAny(): bool                   { return false; }

    // ── Base query with eager loads and instalment count ────────────────────
    public static function getTableQuery(): Builder
    {
        return parent::getTableQuery()
            ->select('transactions.*')
            ->selectRaw(
                '(SELECT COUNT(*) FROM transactions t2
                  WHERE t2.op_document_id = transactions.op_document_id
                    AND t2.deleted_at IS NULL) AS total_tx_count'
            )
            ->with([
                'logs',
                'status',
                'operativeDoc.business.reinsurer',
                'operativeDoc.insureds.company',
            ])
            ->join('operative_docs', 'transactions.op_document_id', '=', 'operative_docs.id')
            ->join('businesses',     'operative_docs.business_id',  '=', 'businesses.id')
            ->join('reinsurers',     'businesses.reinsurer_id',     '=', 'reinsurers.id')
            ->orderBy('reinsurers.short_name')
            ->orderBy('transactions.op_document_id')
            ->orderBy('transactions.index');
    }

    // ── Table ───────────────────────────────────────────────────────────────
    public static function table(Table $table): Table
    {
        $money = fn ($v) => $v !== null
            ? '$' . number_format((float) $v, 2)
            : '—';

        $date = fn ($v) => $v
            ? \Carbon\Carbon::parse($v)->format('d M Y')
            : '—';

        $fee = fn (Transaction $r, int $idx) => ($f = $r->logs->firstWhere('index', $idx)?->banking_fee) && (float) $f > 0
            ? 'Fee: $' . number_format((float) $f, 2)
            : null;

        return $table
            ->columns([
                // ── Identity columns ────────────────────────────────────────
                TextColumn::make('reinsurer')
                    ->label('Reinsurer')
                    ->state(fn (Transaction $r) =>
                        $r->operativeDoc?->business?->reinsurer?->short_name ?? '—'
                    )
                    ->searchable(false)
                    ->wrap(),

                TextColumn::make('instalment')
                    ->label('Instalment')
                    ->state(fn (Transaction $r) =>
                        $r->index . ' of ' . ($r->total_tx_count ?? '?')
                    )
                    ->alignCenter(),

                TextColumn::make('insured')
                    ->label('Insured')
                    ->state(fn (Transaction $r) =>
                        $r->operativeDoc?->insureds?->first()?->company?->name ?? '—'
                    )
                    ->limit(35)
                    ->tooltip(fn (Transaction $r) =>
                        $r->operativeDoc?->insureds?->first()?->company?->name
                    )
                    ->wrap(),

                TextColumn::make('business_id')
                    ->label('Business Id')
                    ->state(fn (Transaction $r) =>
                        $r->operativeDoc?->business?->business_code ?? '—'
                    )
                    ->searchable(false),

                TextColumn::make('status_label')
                    ->label('Status')
                    ->state(fn (Transaction $r) =>
                        $r->status?->transaction_status ?? '—'
                    )
                    ->badge()
                    ->color(fn (?string $state) => match ($state) {
                        'Completed'  => 'success',
                        'In process' => 'warning',
                        default      => 'gray',
                    }),

                ViewColumn::make('lifecycle_progress')
                    ->label('Progress')
                    ->view('filament.components.transaction-progress-column'),

                // ── Section 1: Cliente → Aseguradora ───────────────────────
                ColumnGroup::make('Cliente → Aseguradora', [
                    TextColumn::make('log1_gross')
                        ->label('Gross Premium')
                        ->state(fn (Transaction $r) =>
                            $money($r->logs->firstWhere('index', 1)?->gross_amount_calc)
                        )
                        ->alignRight(),

                    TextColumn::make('log1_date')
                        ->label('Payment Date')
                        ->state(fn (Transaction $r) =>
                            $date($r->logs->firstWhere('index', 1)?->received_date)
                        )
                        ->alignCenter(),
                ]),

                // ── Section 2: Aseguradora → Reaseguradora ─────────────────
                ColumnGroup::make('Aseguradora → Reaseguradora', [
                    TextColumn::make('log2_net')
                        ->label('Net Premium (FT)')
                        ->state(fn (Transaction $r) =>
                            $money($r->logs->firstWhere('index', 2)?->gross_amount_calc)
                        )
                        ->description(fn (Transaction $r) => $fee($r, 2))
                        ->alignRight(),

                    TextColumn::make('log2_date')
                        ->label('Cedant Date')
                        ->state(fn (Transaction $r) =>
                            $date($r->logs->firstWhere('index', 2)?->sent_date)
                        )
                        ->alignCenter(),
                ]),

                // ── Section 3: Reaseguradora → Cautiva ─────────────────────
                ColumnGroup::make('Reaseguradora → Cautiva', [
                    TextColumn::make('log3_amount')
                        ->label('Cedant Payment')
                        ->state(fn (Transaction $r) =>
                            $money($r->logs->firstWhere('index', 3)?->gross_amount_calc)
                        )
                        ->description(fn (Transaction $r) => $fee($r, 3))
                        ->alignRight(),

                    TextColumn::make('log3_date')
                        ->label('Reins. Date')
                        ->state(fn (Transaction $r) =>
                            $date($r->logs->firstWhere('index', 3)?->sent_date)
                        )
                        ->alignCenter(),
                ]),

                // ── Final ───────────────────────────────────────────────────
                TextColumn::make('net_premium_paid')
                    ->label('Net Premium Paid')
                    ->state(fn (Transaction $r) =>
                        $money($r->logs->sortBy('index')->last()?->net_amount)
                    )
                    ->alignRight()
                    ->weight('bold'),
            ])

            // ── Filters ─────────────────────────────────────────────────────
            ->filters([
                SelectFilter::make('reinsurer_id')
                    ->label('Reinsurer')
                    ->options(fn () =>
                        Reinsurer::orderBy('short_name')->pluck('short_name', 'id')
                    )
                    ->query(fn (Builder $query, array $data) =>
                        isset($data['value']) && $data['value']
                            ? $query->where('reinsurers.id', $data['value'])
                            : $query
                    ),

                SelectFilter::make('transaction_status_id')
                    ->label('Status')
                    ->options(fn () =>
                        TransactionStatus::pluck('transaction_status', 'id')
                    ),

                Filter::make('due_date')
                    ->label('Due Date Range')
                    ->schema([
                        DatePicker::make('from')->label('From'),
                        DatePicker::make('until')->label('Until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['from']  ?? null, fn ($q) => $q->where('transactions.due_date', '>=', $data['from']))
                            ->when($data['until'] ?? null, fn ($q) => $q->where('transactions.due_date', '<=', $data['until']));
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['from']  ?? null) $indicators[] = 'Due from: '  . \Carbon\Carbon::parse($data['from'])->format('d M Y');
                        if ($data['until'] ?? null) $indicators[] = 'Due until: ' . \Carbon\Carbon::parse($data['until'])->format('d M Y');
                        return $indicators;
                    }),
            ])
            ->filtersFormColumns(3)

            // ── Row actions ─────────────────────────────────────────────────
            ->recordActions([
                Action::make('view_transaction')
                    ->label('')
                    ->icon('heroicon-o-eye')
                    ->tooltip('View Transaction')
                    ->url(fn (Transaction $r) =>
                        \App\Filament\Resources\Transactions\TransactionResource::getUrl('view', ['record' => $r])
                    ),
            ])

            ->searchable(false)
            ->paginated([25, 50, 100]);
    }

    // ── Pages ────────────────────────────────────────────────────────────────
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPaymentFlow::route('/'),
        ];
    }
}
