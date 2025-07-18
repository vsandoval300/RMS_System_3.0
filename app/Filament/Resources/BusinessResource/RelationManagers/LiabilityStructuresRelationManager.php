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
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\TextInput;
use Filament\Support\RawJs;

class LiabilityStructuresRelationManager extends RelationManager
{
    protected static string $relationship = 'LiabilityStructures';

    public static function getCreateFormHeading(): string
    {
        return 'New Liability Structure';
    }

    public static function getEditFormHeading(): string
    {
        return 'Edit Liability Structure';
    }

    public function form(Form $form): Form
    {
       return $form
        ->schema([
            Section::make('Liability Details')
                ->schema([
                    Grid::make(12)
                    ->schema([
                        /* Forms\Components\TextInput::make('index')
                            ->label('Index')
                            ->disabled()
                            ->dehydrated(false) // 👈 evita que se guarde desde el form
                            ->columnSpan(2), */

                        Forms\Components\Select::make('coverage_id')
                            ->label('Coverage')
                            ->options(fn () =>
                                \App\Models\Coverage::query()
                                    ->orderBy('name')
                                    ->pluck('name', 'id')
                            )
                            ->searchable()
                            ->required()
                            ->columnSpan(7),
                        
                        Section::make()
                        ->schema([
                            Forms\Components\Radio::make('cls')
                            ->label('CSL')
                            ->options([
                                true => 'Yes',
                                false => 'No',
                            ])
                            ->helperText('Combined Single Limit')
                            ->inline() // horizontal
                            ->default(false) // "No" preseleccionado
                            ->required()
                        ])
                        ->columnSpan(5)
                        ->compact()
                        ->extraAttributes(['class' => 'h-full flex items-center justify-center bg-gray-800 rounded-lg'])
                    ]),
                ])
                ->columns(1)
                ->compact()
                ->collapsible(), // opcional: permite colapsar la sección

            Section::make('Liability Scope')
    ->schema([
        Grid::make(12)
            ->schema([
                Forms\Components\TextInput::make('limit')
                    ->label('Limit')
                    ->required()
                    ->mask(
                        RawJs::make(<<<'JS'
                            $money($input, '.', ',', 0)
                        JS)
                    )
                    ->dehydrateStateUsing(fn ($state) => str_replace(',', '', $state))
                    ->columnSpan(3), // mitad de lo que era antes (6 → 3)
                    

                Forms\Components\Textarea::make('limit_desc')
                    ->label('Description')
                    ->required()
                    ->rows(2)
                    ->columnSpan(9), // 1.5x lo que era antes (6 → 9)
            ]),

            Grid::make(12)
                ->schema([
                    Forms\Components\TextInput::make('sublimit')
                        ->label('Sublimit')
                        ->mask(
                            RawJs::make(<<<'JS'
                                $money($input, '.', ',', 0)
                            JS)
                        )
                        ->dehydrateStateUsing(fn ($state) => str_replace(',', '', $state))
                        ->columnSpan(3),
                        

                    Forms\Components\Textarea::make('sublimit_desc')
                        ->label('Description')
                        ->rows(2)
                        ->columnSpan(9),
                ]),

            Grid::make(12)
                ->schema([
                    Forms\Components\TextInput::make('deductible')
                        ->label('Deductible')
                        ->mask(
                            RawJs::make(<<<'JS'
                                $money($input, '.', ',', 0)
                            JS)
                        )
                        ->dehydrateStateUsing(fn ($state) => str_replace(',', '', $state))
                        ->columnSpan(3),

                    Forms\Components\Textarea::make('deductible_desc')
                        ->label('Description')
                        ->rows(2)
                        ->columnSpan(9),
                ]),
        ])
        ->columns(1)
        ->compact()
        ->collapsible(),
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
                        'style' => 'width: 275px; white-space: normal; vertical-align: top;',
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
                        'style' => 'width: 275px; white-space: normal; vertical-align: top;',
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
               
                ]);
            /* ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]); */
    }
}
