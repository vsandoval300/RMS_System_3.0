<?php

namespace App\Filament\Resources\Businesses\RelationManagers;

use App\Models\Country;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use App\Models\Coverage;
use Filament\Forms\Components\Radio;
use Filament\Actions\CreateAction;
use Filament\Actions\ActionGroup;
use Filament\Actions\ViewAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Forms;
use Filament\Tables\Grouping\Group;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Support\Enums\VerticalAlignment;
use Filament\Forms\Components\TextInput;
use Filament\Support\RawJs;
use Illuminate\Database\Eloquent\Model;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;


class LiabilityStructuresRelationManager extends RelationManager
{
    protected static string $relationship = 'LiabilityStructures';
    protected static string | \BackedEnum | null $icon = 'heroicon-o-shield-check';

    public static function getCreateFormHeading(): string
    {
        return 'New Liability Structure';
    }

    public static function getEditFormHeading(): string
    {
        return 'Edit Liability Structure';
    }

   

   

    public function form(Schema $schema): Schema
    {
       // Normalizador: '' -> null, '1,000' -> '1000'
       $toNumber = fn ($state) => filled($state) ? str_replace(',', '', $state) : null;

       return $schema
        ->components([
            Section::make('Liability Details')
            ->columnSpan('full')
                ->schema([
                    Grid::make(12)
                    ->schema([
                        Select::make('coverage_id')
                            ->label('Coverage')
                            ->options(fn () =>
                                Coverage::query()
                                    ->orderBy('name')
                                    ->pluck('name', 'id')
                            )
                            ->searchable()
                            ->preload()
                            ->optionsLimit(300)
                            ->required()
                            ->columnSpan(5),

                        Select::make('country_id')
                            ->label('Country')

                            ->options(function () {
                                $business = $this->getOwnerRecord();

                                return Country::query()
                                    ->where('region_id', $business->region_id)
                                    ->orderBy('alpha_3')
                                    ->pluck('alpha_3', 'id');
                            })

                            ->getSearchResultsUsing(function (string $search) {
                                $business = $this->getOwnerRecord();

                                return Country::query()
                                    ->where('region_id', $business->region_id)
                                    ->where(function ($query) use ($search) {
                                        $query->where('alpha_3', 'like', "%{$search}%")
                                            ->orWhere('name', 'like', "%{$search}%");
                                    })
                                    ->orderBy('alpha_3')
                                    ->pluck('alpha_3', 'id');
                            })

                            ->searchable()
                            ->preload()
                            ->columnSpan(4),

                        //Section::make()
                        //->schema([
                            Radio::make('cls')
                            ->label('CSL')
                            ->options([
                                true => 'Yes',
                                false => 'No',
                            ])
                            ->helperText('Combined Single Limit')
                            ->inline() // horizontal
                            ->default(false) // "No" preseleccionado
                            ->required()
                        //])
                        ->columnSpan(3)
                        //->compact()
                        ->extraAttributes(['class' => 'h-full flex items-center justify-center bg-gray-800 rounded-lg'])
                    ]),
                ])
                ->columns(1)
                ->compact()
                ->collapsible(), // opcional: permite colapsar la sección

            Section::make('Liability Scope')
                ->columnSpan('full')
                ->schema([
                    Grid::make(12)
                        ->columnSpan('full')
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
                            ->columnSpan('full')
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
                            ->columnSpan('full')
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

                TextColumn::make('country.alpha_3')
                    ->verticalAlignment(VerticalAlignment::Start)
                    ->label('Country')
                    ->badge()
                    ->color('info')
                    ->sortable()
                    ->searchable()
                    ->tooltip(fn ($record) => $record->country?->name),

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
                CreateAction::make()
                    ->label('➕ New Liability Structure')
                    ->createAnother(false)            // 👈 oculta "Create & create another"
                    ->modalHeading('➕ New Liability Structure')   // 👈 título del modal
                    ->modalSubmitActionLabel('Create')// (opcional) etiqueta del botón principal
                    ->modalCancelActionLabel('Cancel')// (opcional) etiqueta del botón cancelar
                    // ✅ NUEVO: Modal persistente (no cerrar por click fuera / ESC)
                    ->closeModalByClickingAway(false)
                    ->closeModalByEscaping(false), // opcional, si quieres que ESC no lo cierre

                    /* Tables\Actions\Action::make('close')
                    ->label('Close')
                    ->icon('heroicon-o-x-mark')
                    ->color('gray')
                    ->outlined()
                    ->url(route('filament.admin.resources.businesses.index')),  */
            ])

            ->recordActions([
                    ActionGroup::make([
                    ViewAction::make()
                        // ✅ NUEVO: Modal persistente (no cerrar por click fuera / ESC)
                        ->closeModalByClickingAway(false)
                        ->closeModalByEscaping(false), // opcional, si quieres que ESC no lo cierre
                    EditAction::make()
                        ->modalHeading('📝 Modifying Liability Structure') // 👈 título del modal
                        // ✅ NUEVO: Modal persistente (no cerrar por click fuera / ESC)
                        ->closeModalByClickingAway(false)
                        ->closeModalByEscaping(false), // opcional, si quieres que ESC no lo cierre
                    DeleteAction::make(),
                    ]),

                ]);
            /* ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]); */
    }
}
