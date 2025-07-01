<?php

namespace App\Filament\Resources\ReinsurersResource\RelationManagers;

use App\Models\Holding;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\TextColumn;

class ReinsurerHoldingsRelationManager extends RelationManager
{
    protected static string $relationship = 'reinsurerHoldings';
    protected static ?string $title = 'Holdings';

    public function form(Form $form): Form
    {
        return $form->schema([
            Select::make('holding_id')
                ->label('Holding')
                ->options(Holding::pluck('name', 'id'))
                ->required(),
            TextInput::make('percentage')
                ->numeric()->minValue(0)->maxValue(100)->required(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('holding.name')
            ->columns([
                TextColumn::make('holding.name')->label('Holding'),
                TextColumn::make('percentage')->label('%'),
        ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
