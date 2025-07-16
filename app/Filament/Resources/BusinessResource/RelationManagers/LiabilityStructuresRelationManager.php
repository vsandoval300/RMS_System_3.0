<?php

namespace App\Filament\Resources\BusinessResource\RelationManagers;

use Filament\Forms;
use Filament\Tables\Grouping\Group;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Support\Enums\VerticalAlignment;

class LiabilityStructuresRelationManager extends RelationManager
{
    protected static string $relationship = 'LiabilityStructures';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('index')
                ->required()
                ->maxLength(255),

                Forms\Components\Select::make('coverage_id')
                    ->label('Coverage')
                    ->relationship('coverage', 'name')
                    ->searchable()
                    ->required(),

                Forms\Components\Toggle::make('cls')
                    ->label('C.L.S.'),

                Forms\Components\TextInput::make('limit')
                    ->numeric()
                    ->label('Limit'),

                Forms\Components\Textarea::make('limit_desc')
                    ->label('Limit Description')
                    ->rows(2),

                Forms\Components\TextInput::make('sublimit')
                    ->numeric()
                    ->label('Sublimit'),

                Forms\Components\Textarea::make('sublimit_desc')
                    ->label('Sublimit Description')
                    ->rows(2),

                Forms\Components\TextInput::make('deductible')
                    ->numeric()
                    ->label('Deductible'),

                Forms\Components\Textarea::make('deductible_desc')
                    ->label('Deductible Description')
                    ->rows(2),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('index')
            
            //->defaultGroup('lineOfBusiness.name')
            ->groups([
                Group::make('coverage.name')
                    ->label('Coverage')
                    ->collapsible()
                    ->orderQueryUsing(fn (Builder $query, string $direction) =>
                        $query->orderBy('index', $direction)
                    ),
            ])
            ->defaultGroup('coverage.name')
            ->defaultSort('coverage.name')  
            


            ->columns([
                Tables\Columns\TextColumn::make('index')
                    ->verticalAlignment(VerticalAlignment::Start) 
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('coverage.name')
                    ->label('Coverage')
                    ->verticalAlignment(VerticalAlignment::Start) 
                    ->sortable()
                    ->searchable()
                    ->wrap() // 👈 permite múltiples líneas
                    ->toggleable(isToggledHiddenByDefault: true) // 👈 Oculta por defecto pero sigue disponible
                    ->extraAttributes([
                        'style' => 'width: 250px; white-space: normal; vertical-align: top;',
                    ]),
                
                Tables\Columns\TextColumn::make('limit')
                    ->label('Limit')
                    ->sortable()
                    ->verticalAlignment(VerticalAlignment::Start)
                    ->formatStateUsing(fn ($state) => $state == 0 ? null : number_format($state, 0)),

                Tables\Columns\TextColumn::make('limit_desc')
                    ->label('Limit Description')
                    ->label('Limit Description')
                    ->verticalAlignment(VerticalAlignment::Start) 
                    ->wrap()         // 👈 permite varias líneas
                    ->extraAttributes([
                        'style' => 'width: 280px; white-space: normal; vertical-align: top;',
                    ]),

                Tables\Columns\TextColumn::make('sublimit')
                    ->label('Sublimit')
                    ->numeric()
                    ->verticalAlignment(VerticalAlignment::Start) 
                    ->sortable()
                    ->formatStateUsing(fn ($state) => $state == 0 ? null : number_format($state, 0)),

                Tables\Columns\TextColumn::make('sublimit_desc')
                    ->label('Sublimit Description')
                    ->verticalAlignment(VerticalAlignment::Start) 
                    ->wrap()
                    ->extraAttributes([
                        'style' => 'width: 280px; white-space: normal; vertical-align: top;',
                    ]),

                Tables\Columns\TextColumn::make('deductible')
                    ->label('Deductible')
                    ->numeric()
                    ->verticalAlignment(VerticalAlignment::Start) 
                    ->sortable()
                    ->formatStateUsing(fn ($state) => $state == 0 ? null : number_format($state, 0)),

                Tables\Columns\TextColumn::make('deductible_desc')
                    ->label('Deductible Description')
                    ->limit(40)
                    ->verticalAlignment(VerticalAlignment::Start) 
                     ->extraAttributes([
                        'style' => 'width: 200px; white-space: normal; vertical-align: top;',
                    ]),    
            ])

            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                    Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                    ]),
               
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
