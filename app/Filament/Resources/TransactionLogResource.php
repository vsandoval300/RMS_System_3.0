<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransactionLogResource\Pages;
use App\Models\TransactionLog;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Columns\SelectColumn;

class TransactionLogResource extends \Filament\Resources\Resource
{
    protected static ?string $model = TransactionLog::class;

    // Ocúltalo del menú si no quieres navegación directa:
    protected static bool $shouldRegisterNavigation = false;
    protected static ?string $navigationGroup = 'Underwritten';
    protected static ?string $navigationIcon = 'heroicon-o-queue-list';
    protected static ?string $slug = 'transaction-logs';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Operational fields')
                ->schema([
                    Forms\Components\DatePicker::make('sent_date')
                        ->label('Sent')
                        ->native(false),

                    Forms\Components\DatePicker::make('received_date')
                        ->label('Received')
                        ->native(false),

                    Forms\Components\TextInput::make('banking_fee')
                        ->label('Banking Fee')
                        ->numeric()
                        ->step(0.01)
                        ->minValue(0),

                    Forms\Components\Select::make('status')
                        ->options([
                            'Pending'     => 'Pending',
                            'In Process'  => 'In Process',
                            'Completed'   => 'Completed',
                            'Rejected'    => 'Rejected',
                        ])
                        ->required(),
                ])->columns(4),

            Forms\Components\Section::make('Read-only')
                ->collapsed()
                ->schema([
                    Forms\Components\TextInput::make('transaction_id')->disabled(),
                    Forms\Components\TextInput::make('index')->disabled(),
                    Forms\Components\TextInput::make('exch_rate')->disabled(),
                    Forms\Components\TextInput::make('gross_amount')->disabled(),
                    Forms\Components\TextInput::make('commission_discount')->disabled(),
                    Forms\Components\TextInput::make('net_amount')->disabled(),
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
                Tables\Columns\TextColumn::make('transaction.index')
                    ->label('Inst.')
                    ->badge()
                    ->sortable(),

                Tables\Columns\TextColumn::make('index')
                    ->label('#')
                    ->sortable(),

                Tables\Columns\TextColumn::make('deduction.concept')
                    ->label('Deduction')
                    ->searchable(),

                Tables\Columns\TextColumn::make('fromPartner.short_name')
                    ->label('From')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('toPartner.short_name')
                    ->label('To')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('exch_rate')
                    ->label('Rate')
                    ->numeric(5)
                    ->toggleable(),

                Tables\Columns\TextColumn::make('gross_amount')
                    ->label('Gross')
                    ->money(fn () => 'USD')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('commission_discount')
                    ->label('Discount')
                    ->money(fn () => 'USD')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('banking_fee')
                    ->label('Bank Fee')
                    ->money(fn () => 'USD')
                    ->sortable()
                    ->toggleable(), // ← sin inlineEditable()

                Tables\Columns\TextColumn::make('net_amount')
                    ->label('Net')
                    ->money(fn () => 'USD')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('sent_date')
                    ->label('Sent')
                    ->date(),        // ← sin inlineEditable()

                Tables\Columns\TextColumn::make('received_date')
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
            ->actions([
                Tables\Actions\EditAction::make(), // edita banking_fee y fechas en el form
                Tables\Actions\ViewAction::make(),
            ])

            ->bulkActions([
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
            'index' => Pages\ListTransactionLogs::route('/'),
            'edit'  => Pages\EditTransactionLog::route('/{record}/edit'),
            // 🚫 no create: los logs se generan automáticamente
        ];
    }
}
