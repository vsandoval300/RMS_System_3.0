<?php

namespace App\Filament\Resources\CostSchemeResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use App\Models\Partner;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CostNodexesRelationManager extends RelationManager
{
    protected static string $relationship = 'costNodexes';
    protected static ?string $title = 'Cost Nodes'; // 👈 esto cambia el título

    protected static ?string $recordTitleAttribute = 'concept';
    protected static ?string $pluralModelLabel = 'Cost Nodes';
    protected static ?string $modelLabel = 'Cost Node';

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('concept')
                ->label('Concept')
                ->relationship('deduction', 'concept') // ← usa la relación
                ->searchable()
                ->preload()
                ->required(),

            Forms\Components\TextInput::make('value')
                ->numeric()
                ->required(),

            Forms\Components\Select::make('partner_id')
                ->label('Partner')
                ->options(Partner::all()->pluck('name', 'id'))
                ->searchable()
                ->preload()
                ->required(),

            Forms\Components\TextInput::make('referral_partner')
                ->label('Referral Partner')
                ->maxLength(255),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('index')
                    ->sortable()
                    ->label('Index'), // 👈 nueva columna
                Tables\Columns\TextColumn::make('deduction.concept')
                    ->label('Concept')
                    ->searchable(),
                Tables\Columns\TextColumn::make('value')
                    ->label('Value (%)')
                    ->formatStateUsing(fn ($state) => $state * 100 . '%'),
                Tables\Columns\TextColumn::make('partner.name')
                    ->label('Partner'),
                Tables\Columns\TextColumn::make('referral_partner'),
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
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }
}