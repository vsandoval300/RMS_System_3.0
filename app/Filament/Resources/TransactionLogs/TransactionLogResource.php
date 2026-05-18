<?php

namespace App\Filament\Resources\TransactionLogs;

use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use App\Filament\Resources\TransactionLogs\Pages\ListTransactionLogs;
use App\Filament\Resources\TransactionLogs\Pages\EditTransactionLog;
use App\Filament\Resources\TransactionLogResource\Pages;
use App\Models\TransactionLog;
use Filament\Forms;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Columns\SelectColumn;

class TransactionLogResource extends Resource
{
    protected static ?string $model = TransactionLog::class;

    // Ocúltalo del menú si no quieres navegación directa:
    protected static bool $shouldRegisterNavigation = false;
    protected static string | \UnitEnum | null $navigationGroup = 'Underwritten';
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-queue-list';
    protected static ?string $slug = 'transaction-logs';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Operational fields')
                ->columnSpanFull()
                ->schema([
                    DatePicker::make('sent_date')
                        ->label('Sent')
                        ->native(false),

                    DatePicker::make('received_date')
                        ->label('Received')
                        ->native(false),

                    TextInput::make('banking_fee')
                        ->label('Banking Fee')
                        ->numeric()
                        ->step(0.01)
                        ->minValue(0),

                    Select::make('status')
                        ->options([
                            'Pending'     => 'Pending',
                            'In Process'  => 'In Process',
                            'Completed'   => 'Completed',
                            'Rejected'    => 'Rejected',
                        ])
                        ->required(),
                ])->columns(4),

            Section::make('Read-only')
                ->columnSpanFull()
                ->collapsed()
                ->schema([
                    TextInput::make('transaction_id')->disabled(),
                    TextInput::make('index')->disabled(),
                    TextInput::make('exch_rate')->disabled(),
                    TextInput::make('gross_amount')->disabled(),
                    TextInput::make('commission_discount')->disabled(),
                    TextInput::make('net_amount')->disabled(),
                ])->columns(3),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            // ⚠️ Orden por transacción.index y luego por log.index
            ->defaultSort(function (Builder $query) {
                $query->select('transaction_logs.*')
                    ->leftJoin('transactions', 'transactions.id', '=', 'transaction_logs.transaction_id')
                    ->orderBy('transactions.index')
                    ->orderBy('transaction_logs.index');
            })
            ->columns([
                TextColumn::make('transaction.index')
                    ->label('Inst.')
                    ->badge()
                    ->sortable(),

                TextColumn::make('index')
                    ->label('#')
                    ->sortable(),

                TextColumn::make('deduction.concept')
                    ->label('Deduction')
                    ->searchable(),

                TextColumn::make('fromPartner.short_name')
                    ->label('From')
                    ->toggleable(),

                TextColumn::make('toPartner.short_name')
                    ->label('To')
                    ->toggleable(),

                TextColumn::make('exch_rate')
                    ->label('Rate')
                    ->numeric(5)
                    ->toggleable(),

                TextColumn::make('gross_amount')
                    ->label('Gross')
                    ->money(fn () => 'USD')
                    ->toggleable(),

                TextColumn::make('commission_discount')
                    ->label('Discount')
                    ->money(fn () => 'USD')
                    ->toggleable(),

                TextColumn::make('banking_fee')
                    ->label('Bank Fee')
                    ->money(fn () => 'USD')
                    ->sortable()
                    ->toggleable(), // ← sin inlineEditable()

                TextColumn::make('net_amount')
                    ->label('Net')
                    ->money(fn () => 'USD')
                    ->toggleable(),

                TextColumn::make('sent_date')
                    ->label('Sent')
                    ->date(),        // ← sin inlineEditable()

                TextColumn::make('received_date')
                    ->label('Received')
                    ->date(),        // ← sin inlineEditable()

                // ✅ Inline real para status con SelectColumn (v3)
                SelectColumn::make('status')
                    ->label('Status')
                    ->options([
                        'Pending'    => 'Pending',
                        'In Process' => 'In Process',
                        'Completed'  => 'Completed',
                        'Rejected'   => 'Rejected',
                    ])
                    ->sortable(),
            ])
            ->recordActions([
                EditAction::make(), // edita banking_fee y fechas en el form
                ViewAction::make(),
            ])

            ->toolbarActions([
                // sin delete/create
            ]);
    }

    /**
     * Filtro por documento: ?op_document_id=XXX
     */
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()
            ->with(['transaction', 'deduction', 'fromPartner', 'toPartner']);

        if ($docId = request()->query('op_document_id')) {
            $query->whereHas('transaction', fn ($q) => $q->where('op_document_id', $docId));
        }

        return $query;
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTransactionLogs::route('/'),
            'edit'  => EditTransactionLog::route('/{record}/edit'),
            // 🚫 no create: los logs se generan automáticamente
        ];
    }
}
