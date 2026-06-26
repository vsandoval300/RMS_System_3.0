<?php

namespace App\Filament\Resources\CostSchemes\RelationManagers;

use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms;
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

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('concept')
                ->label('Concept')
                ->relationship('deduction', 'concept') // ← usa la relación
                ->searchable()
                ->preload()
                ->required(),

            TextInput::make('value')
                ->numeric()
                ->required(),

            Select::make('partner_id')
                ->label('Partner')
                ->options(Partner::all()->pluck('name', 'id'))
                ->searchable()
                ->preload()
                ->optionsLimit(300)
                ->required(),

            TextInput::make('referral_partner')
                ->label('Referral Partner')
                ->maxLength(255),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('index')
                    ->sortable()
                    ->label('Index'), // 👈 nueva columna
                TextColumn::make('deduction.concept')
                    ->label('Concept')
                    ->searchable(),
                TextColumn::make('value')
                    ->label('Value (%)')
                    ->formatStateUsing(fn ($state) => $state * 100 . '%'),
                TextColumn::make('partner.name')
                    ->label('Partner'),
                TextColumn::make('referral_partner'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                DeleteBulkAction::make(),
            ]);
    }
}