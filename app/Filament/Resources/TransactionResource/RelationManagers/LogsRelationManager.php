<?php

namespace App\Filament\Resources\TransactionResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Support\RawJs;

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

    public function form(Form $form): Form
    {
        return $form->schema([
            Section::make()
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
                ->columns(2)
                ->schema([
                    DatePicker::make('sent_date'),
                    DatePicker::make('received_date'),
                ]),

            Section::make()
                ->columns(2)
                ->schema([
                    TextInput::make('exch_rate')->numeric(),
                    TextInput::make('status')
                        ->maxLength(30)
                        ->disabled()
                        ->dehydrated(false),
                ]),

            Section::make()
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
                ]),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('index')->sortable(),

                TextColumn::make('deduction.concept')
                    ->label('Deduction')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('fromPartner.short_name')
                    ->label('From Partner')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('toPartner.short_name')
                    ->label('To Partner')
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
                TextColumn::make('status')->badge(),
            ])
            ->defaultSort('index', 'asc')
            ->headerActions([
                // ✅ NO CreateAction
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                // ✅ NO DeleteBulkAction
            ]);
    }
}
