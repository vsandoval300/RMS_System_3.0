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
use Filament\Forms\Components\TextInput;
use Filament\Support\RawJs;
use Illuminate\Database\Eloquent\Model;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;


class LiabilityStructuresRelationManager extends RelationManager
{
    protected static string $relationship = 'LiabilityStructures';
    protected static ?string $icon = 'heroicon-o-shield-check';

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
       // Normalizador: '' -> null, '1,000' -> '1000'
       $toNumber = fn ($state) => filled($state) ? str_replace(',', '', $state) : null;

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

                        Select::make('coverage_id')
                            ->label('Coverage')
                            ->options(fn () =>
                                \App\Models\Coverage::query()
                                    ->orderBy('name')
                                    ->pluck('name', 'id')
                            )
                            ->searchable()
                            ->preload()
                            ->optionsLimit(300)
                            ->required()
                            ->columnSpan(7),

                        /* Select::make('country_id')
                            ->label('Country')
                            ->options(function () {
                                return Country::orderBy('name')
                                    ->get()
                                    ->mapWithKeys(fn ($country) => [
                                        $country->id => "{$country->alpha_3} - {$country->name}"
                                    ]);
                            })
                            ->searchable()
                            ->preload()
                            ->optionsLimit(300)
                            ->required()
                            ->placeholder('Select a country')
                            ->helperText('Choose the reinsurer\'s country.'), */

                            
                        
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
                            TextInput::make('limit')
                                ->label('Limit')
                                ->required()
                                ->mask(
                                    RawJs::make(<<<'JS'
                                        $money($input, '.', ',', 0)
                                    JS)
                                )
                                ->dehydrateStateUsing($toNumber)   // "5,000,000" -> "5000000"
                                ->placeholder('1,000,000.00')
                                ->helperText('Enter an amount.')
                                ->columnSpan(3),
                                

                            Textarea::make('limit_desc')
                                ->label('Description')
                                ->required()
                                ->rows(2)
                                ->columnSpan(9) // 1.5x lo que era antes (6 → 9)
                                ->placeholder('Fill in limit description'),
                        
                        ]),

                        Grid::make(12)
                            ->schema([
                                TextInput::make('sublimit')
                                    ->label('Sublimit')
                                    ->mask(
                                        RawJs::make(<<<'JS'
                                            $money($input, '.', ',', 0)
                                        JS)
                                    )
                                    ->dehydrateStateUsing($toNumber)   // '' -> null (no 22P02)
                                    ->placeholder('1,000,000.00')
                                    ->helperText('Enter an amount.')
                                    ->columnSpan(3),
                                    

                                Textarea::make('sublimit_desc')
                                    ->label('Description')
                                    ->rows(2)
                                    ->columnSpan(9)
                                    ->placeholder('Fill in sublimit description'),
                            ]),

                        Grid::make(12)
                            ->schema([
                                TextInput::make('deductible')
                                    ->label('Deductible')
                                    ->mask(
                                        RawJs::make(<<<'JS'
                                            $money($input, '.', ',', 0)
                                        JS)
                                    )
                                    ->dehydrateStateUsing($toNumber)   // '' -> null (no 22P02)
                                    ->placeholder('1,000,000.00 or 30')
                                    ->helperText('Enter an amount or a number of days.')
                                    ->columnSpan(3),

                                Textarea::make('deductible_desc')
                                    ->label('Description')
                                    ->rows(2)
                                    ->columnSpan(9)
                                    ->placeholder('Fill in deductible description'),
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
            //->preload()
            //->optionsLimit(300)  
            

            ->columns([
                TextColumn::make('index')
                    ->verticalAlignment(VerticalAlignment::Start) 
                    ->sortable()
                    ->searchable(),

                TextColumn::make('coverage.name')
                    ->label('Coverage')
                    ->verticalAlignment(VerticalAlignment::Start) 
                    ->sortable()
                    ->searchable()
                    ->wrap() // 👈 permite múltiples líneas
                    ->toggleable(isToggledHiddenByDefault: true) // 👈 Oculta por defecto pero sigue disponible
                    ->extraAttributes([
                        'style' => 'width: 250px; white-space: normal; vertical-align: top;',
                    ]),
                
                TextColumn::make('limit')
                    ->label('Limit')
                    ->sortable()
                    ->verticalAlignment(VerticalAlignment::Start)
                    ->formatStateUsing(fn ($state) => $state == 0 ? null : number_format($state, 0)),

                TextColumn::make('limit_desc')
                    ->label('Limit Description')
                    ->label('Limit Description')
                    ->verticalAlignment(VerticalAlignment::Start) 
                    ->wrap()         // 👈 permite varias líneas
                    ->extraAttributes([
                        'style' => 'width: 275px; white-space: normal; vertical-align: top;',
                    ]),

                TextColumn::make('sublimit')
                    ->label('Sublimit')
                    ->numeric()
                    ->verticalAlignment(VerticalAlignment::Start) 
                    ->sortable()
                    ->formatStateUsing(fn ($state) => $state == 0 ? null : number_format($state, 0)),

                TextColumn::make('sublimit_desc')
                    ->label('Sublimit Description')
                    ->verticalAlignment(VerticalAlignment::Start) 
                    ->wrap()
                    ->extraAttributes([
                        'style' => 'width: 275px; white-space: normal; vertical-align: top;',
                    ]),

                TextColumn::make('deductible')
                    ->label('Deductible')
                    ->numeric()
                    ->verticalAlignment(VerticalAlignment::Start) 
                    ->sortable()
                    ->formatStateUsing(fn ($state) => $state == 0 ? null : number_format($state, 0)),

                TextColumn::make('deductible_desc')
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
                Tables\Actions\CreateAction::make()
                    ->label('➕ New Liability Structure')
                    ->createAnother(false)            // 👈 oculta "Create & create another"
                    ->modalHeading('➕ New Liability Structure')   // 👈 título del modal
                    ->modalSubmitActionLabel('Create')// (opcional) etiqueta del botón principal
                    ->modalCancelActionLabel('Cancel'),// (opcional) etiqueta del botón cancelar


                    /* Tables\Actions\Action::make('close')
                    ->label('Close')
                    ->icon('heroicon-o-x-mark')
                    ->color('gray')
                    ->outlined()
                    ->url(route('filament.admin.resources.businesses.index')),  */
            ])

            ->actions([
                    Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make()
                        ->modalHeading('📝 Modifying Liability Structure'), // 👈 título del modal
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
