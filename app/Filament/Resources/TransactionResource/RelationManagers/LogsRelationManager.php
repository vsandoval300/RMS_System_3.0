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

class LogsRelationManager extends RelationManager
{
    protected static string $relationship = 'logs';  // 👈 nombre de la relación en el modelo Transaction

    protected static ?string $title = 'Transaction Logs'; // 👈 título visible en el tab

    public function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('transaction_code')
                ->label('Transaction Code')
                ->required()
                ->maxLength(50), 

            TextInput::make('index')
                ->numeric(),

            TextInput::make('deduction_type')
                ->maxLength(50),

            TextInput::make('from_entity')
                ->maxLength(100),

            TextInput::make('to_entity')
                ->maxLength(100),

            DatePicker::make('sent_date'),
            DatePicker::make('received_date'),

            TextInput::make('exch_rate')->numeric(),
            TextInput::make('gross_amount')->numeric(),
            TextInput::make('commission_discount')->numeric(),
            TextInput::make('banking_fee')->numeric(),
            TextInput::make('net_amount')->numeric(),
            
            TextInput::make('status')->maxLength(20),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('index')->sortable(),

                // 1) Mostrar el concepto de la deducción
                TextColumn::make('deduction.concept')
                    ->label('Deduction')
                    ->sortable()
                    ->searchable(),

                // 2) Mostrar el short_name del partner origen
                TextColumn::make('fromPartner.short_name')
                    ->label('From Partner')
                    ->sortable()
                    ->searchable(),

                // 3) Mostrar el short_name del partner destino
                TextColumn::make('toPartner.short_name')
                    ->label('To Partner')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('sent_date')->date(),
                TextColumn::make('received_date')->date(),
                TextColumn::make('exch_rate')->numeric(decimalPlaces: 5),
                TextColumn::make('gross_amount')->numeric(2),
                TextColumn::make('net_amount')->numeric(2),
                TextColumn::make('status')->badge(),
            ])
            ->defaultSort('index', 'asc')
            ->filters([])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                //Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

}
