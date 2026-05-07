<?php

namespace App\Filament\Resources\Countries;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Actions\ActionGroup;
use Filament\Actions\ViewAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Resources\Countries\Pages\ListCountries;
use App\Filament\Resources\Countries\Pages\CreateCountries;
use App\Filament\Resources\Countries\Pages\ViewCountries;
use App\Filament\Resources\Countries\Pages\EditCountries;
use App\Filament\Resources\CountriesResource\Pages;
use App\Filament\Resources\CountriesResource\RelationManagers;
use App\Models\Country;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Grid;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Validation\Rules\Unique;
use Filament\Infolists\Components\TextEntry;

class CountriesResource extends Resource
{
    protected static ?string $model = Country::class;
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-minus';
    protected static string | \UnitEnum | null $navigationGroup = 'Resources';
    protected static ?int    $navigationSort  = 4;   // aparecerá primero

    /* ───── NUEVO: burbuja con el total en el menú ───── */
    public static function getNavigationBadge(): ?string
    {
        // Puedes usar self::$model::count() o Reinsurer::count()
        return Country::count();
    }

    /* public static function canCreate(): bool
    {
        // Devuelve false para ocultar el botón “New country”
        return false;
    } */

    

public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make() //Grupo 1
                ->schema([
                    Section::make('Corporate Document Details')
                    ->schema([
                    
                        TextInput::make('name')
                        ->label('Name')
                        ->required()
                        ->unique(
                            ignoreRecord: true,
                            modifyRuleUsing: fn (Unique $rule) => $rule->whereNull('deleted_at')
                        )
                        ->maxLength(255)
                        ->afterStateUpdated(fn ($state, callable $set) => $set('name', ucwords(strtolower($state)))),
                        //->extraAttributes(['class' => 'w-1/2']),
                        //->helperText('First letter of each word will be capitalised.')
                        //->disabled()
                        //->dehydrated(false),   // evita que el valor se envíe al servidor

                        TextInput::make('alpha_2')
                        ->label('Alpha 2')
                        ->required()
                        ->unique(
                            ignoreRecord: true,
                            modifyRuleUsing: fn (Unique $rule) => $rule->whereNull('deleted_at')
                        )
                        ->maxLength(2)
                        ->rule('regex:/^[A-Z]+$/')
                        ->afterStateUpdated(fn ($state, callable $set) => $set('alpha_2', strtoupper($state))),
                        //->extraAttributes(['class' => 'w-1/2']),
                        //->helperText('Only uppercase letters allowed.')
                        //->disabled()
                        //->dehydrated(false),   // evita que el valor se envíe al servidor

                        TextInput::make('alpha_3')
                        ->label('Alpha 3')
                        ->required()
                        ->unique(
                            ignoreRecord: true,
                            modifyRuleUsing: fn (Unique $rule) => $rule->whereNull('deleted_at')
                        )
                        ->maxLength(3)
                        ->rule('regex:/^[A-Z]+$/')
                        ->afterStateUpdated(fn ($state, callable $set) => $set('alpha_3', strtoupper($state))),
                        //->extraAttributes(['class' => 'w-1/2']),
                        //->helperText('Only uppercase letters allowed.')
                        //->disabled()
                        //->dehydrated(false),   // evita que el valor se envíe al servidor

                        TextInput::make('country_code')
                        ->label('Country Code')
                        ->required()
                        ->unique(
                            ignoreRecord: true,
                            modifyRuleUsing: fn (Unique $rule) => $rule->whereNull('deleted_at')
                        )
                        ->numeric()
                        ->minValue(1) // opcional: evita 0 o negativos
                        ->maxValue(999), // opcional: para limitar a 3 dígitos
                        //->extraAttributes(['class' => 'w-1/2']),
                        //->helperText('Only whole numbers allowed.')
                        //->disabled()
                        //->dehydrated(false),   // evita que el valor se envíe al servidor

                        TextInput::make('iso_code')
                        ->label('Iso Code')
                        ->required()
                        ->unique(
                            ignoreRecord: true,
                            modifyRuleUsing: fn (Unique $rule) => $rule->whereNull('deleted_at')
                        )
                        ->maxLength(30)
                        ->rule('regex:/^[A-Z0-9\-:\s]+$/')
                        ->afterStateUpdated(fn ($state, callable $set) => $set('iso_code', strtoupper($state))),
                        //->extraAttributes(['class' => 'w-1/2']),
                        //->helperText('Only uppercase letters, numbers, dash (-), colon (:), and spaces allowed.')
                        //->disabled()
                        //->dehydrated(false),   // evita que el valor se envíe al servidor

                        TextInput::make('am_best_code')
                        ->label('AM Best Code')
                        ->required()
                        ->maxLength(10)
                        ->rule('regex:/^[A-Z0-9\-\s]+$/')
                        ->afterStateUpdated(fn ($state, callable $set) => $set('am_best_code', strtoupper($state))),
                        //->extraAttributes(['class' => 'w-1/2']),
                        //->helperText('Only uppercase letters, numbers, dash (-), and spaces allowed.')
                        //->disabled()
                        //->dehydrated(false),   // evita que el valor se envíe al servidor

                        TextInput::make('latitude')
                        ->label('Latitude')
                        ->required()
                        ->unique(
                            ignoreRecord: true,
                            modifyRuleUsing: fn (Unique $rule) => $rule->whereNull('deleted_at')
                        )
                        ->type('number') // ✅ convierte el input en <input type="number">
                        ->step('any')    // ✅ permite cualquier cantidad de decimales
                        ->minValue(-90)  // límite geográfico para latitud
                        ->maxValue(90),
                        //->extraAttributes(['class' => 'w-1/2']),
                        //->helperText('Enter a decimal value between -90 and 90.')
                        //->disabled()
                        //->dehydrated(false),   // evita que el valor se envíe al servidor

                        TextInput::make('longitude')
                        ->label('longitude')
                        ->required()
                        ->unique(
                            ignoreRecord: true,
                            modifyRuleUsing: fn (Unique $rule) => $rule->whereNull('deleted_at')
                        )
                        ->type('number') // ✅ convierte el input en <input type="number">
                        ->step('any')    // ✅ permite cualquier cantidad de decimales
                        ->minValue(-90)  // límite geográfico para latitud
                        ->maxValue(90),
                        //->extraAttributes(['class' => 'w-1/2']),
                        //->helperText('Enter a decimal value between -90 and 90.')
                        //->disabled()
                        //->dehydrated(false),   // evita que el valor se envíe al servidor

                        Select::make('region_id')
                        ->label('Region')
                        ->relationship('region', 'name')
                        ->searchable()
                        ->preload()
                        ->required(),
                        //->extraAttributes(['class' => 'w-1/2']),

                            
                        
                               
                    ])
                    ->columns(2),
                ])
                ->columnSpanFull(),
            ]);
    }









public static function infolist(Schema $schema): Schema
{
    return $schema->components([
        /* ─────────────────────────  PROFILE  ───────────────────────── */
        Section::make('Country Profile')->schema([
            \Filament\Schemas\Components\Grid::make(2)
                ->extraAttributes(['style' => 'gap: 6px;'])
                ->schema([

                    // Cols 1–2: filas “Label (3) + Value (9)”
                    \Filament\Schemas\Components\Grid::make(1)
                        ->columnSpan(2)
                        ->extraAttributes(['style' => 'row-gap: 0;'])
                        ->schema([

                            // Name
                            \Filament\Schemas\Components\Grid::make(12)
                                ->extraAttributes(['style' => 'border-bottom:1px solid rgba(255,255,255,0.12); padding:2px 0;'])
                                ->schema([
                                    TextEntry::make('name_label')->label('')->state('Name:')->weight('bold')->alignment('right')->columnSpan(3),
                                    TextEntry::make('name_value')->label('')->state(fn ($record) => $record->name ?: '—')->columnSpan(9),
                                ]),

                            // Alpha 2
                            \Filament\Schemas\Components\Grid::make(12)
                                ->extraAttributes(['style' => 'border-bottom:1px solid rgba(255,255,255,0.12); padding:2px 0;'])
                                ->schema([
                                    TextEntry::make('a2_label')->label('')->state('Alpha 2:')->weight('bold')->alignment('right')->columnSpan(3),
                                    TextEntry::make('a2_value')->label('')->state(fn ($record) => $record->alpha_2 ?: '—')->columnSpan(9),
                                ]),

                            // Alpha 3
                            \Filament\Schemas\Components\Grid::make(12)
                                ->extraAttributes(['style' => 'border-bottom:1px solid rgba(255,255,255,0.12); padding:2px 0;'])
                                ->schema([
                                    TextEntry::make('a3_label')->label('')->state('Alpha 3:')->weight('bold')->alignment('right')->columnSpan(3),
                                    TextEntry::make('a3_value')->label('')->state(fn ($record) => $record->alpha_3 ?: '—')->columnSpan(9),
                                ]),

                            // Country Code
                            \Filament\Schemas\Components\Grid::make(12)
                                ->extraAttributes(['style' => 'border-bottom:1px solid rgba(255,255,255,0.12); padding:2px 0;'])
                                ->schema([
                                    TextEntry::make('cc_label')->label('')->state('Country Code:')->weight('bold')->alignment('right')->columnSpan(3),
                                    TextEntry::make('cc_value')->label('')->state(fn ($record) => isset($record->country_code) ? (string) $record->country_code : '—')->columnSpan(9),
                                ]),

                            // ISO Code
                            \Filament\Schemas\Components\Grid::make(12)
                                ->extraAttributes(['style' => 'border-bottom:1px solid rgba(255,255,255,0.12); padding:2px 0;'])
                                ->schema([
                                    TextEntry::make('iso_label')->label('')->state('ISO Code:')->weight('bold')->alignment('right')->columnSpan(3),
                                    TextEntry::make('iso_value')->label('')->state(fn ($record) => $record->iso_code ?: '—')->columnSpan(9),
                                ]),

                            // AM Best Code
                            \Filament\Schemas\Components\Grid::make(12)
                                ->extraAttributes(['style' => 'border-bottom:1px solid rgba(255,255,255,0.12); padding:2px 0;'])
                                ->schema([
                                    TextEntry::make('amb_label')->label('')->state('AM Best Code:')->weight('bold')->alignment('right')->columnSpan(3),
                                    TextEntry::make('amb_value')->label('')->state(fn ($record) => $record->am_best_code ?: '—')->columnSpan(9),
                                ]),

                            // Latitude
                            \Filament\Schemas\Components\Grid::make(12)
                                ->extraAttributes(['style' => 'border-bottom:1px solid rgba(255,255,255,0.12); padding:2px 0;'])
                                ->schema([
                                    TextEntry::make('lat_label')->label('')->state('Latitude:')->weight('bold')->alignment('right')->columnSpan(3),
                                    TextEntry::make('lat_value')->label('')->state(fn ($record) =>
                                        is_numeric($record->latitude) ? number_format((float) $record->latitude, 6) : '—'
                                    )->columnSpan(9),
                                ]),

                            // Longitude
                            \Filament\Schemas\Components\Grid::make(12)
                                ->extraAttributes(['style' => 'border-bottom:1px solid rgba(255,255,255,0.12); padding:2px 0;'])
                                ->schema([
                                    TextEntry::make('lng_label')->label('')->state('Longitude:')->weight('bold')->alignment('right')->columnSpan(3),
                                    TextEntry::make('lng_value')->label('')->state(fn ($record) =>
                                        is_numeric($record->longitude) ? number_format((float) $record->longitude, 6) : '—'
                                    )->columnSpan(9),
                                ]),

                            // Region
                            \Filament\Schemas\Components\Grid::make(12)
                                ->extraAttributes(['style' => 'border-bottom:1px solid rgba(255,255,255,0.12); padding:2px 0;'])
                                ->schema([
                                    TextEntry::make('region_label')->label('')->state('Region:')->weight('bold')->alignment('right')->columnSpan(3),
                                    TextEntry::make('region_value')->label('')->state(fn ($record) => $record->region?->name ?: '—')->columnSpan(9),
                                ]),
                        ]),
                ]),
        ])
        ->maxWidth('5xl')
        ->collapsible(),

        /* ─────────────────────────  AUDIT  ───────────────────────── */
        /* InfoSection::make('Audit Dates')
            ->schema([
                InfoGrid::make(2)
                    ->extraAttributes(['style' => 'gap: 12px;'])
                    ->schema([
                        InfoGrid::make(12)
                            ->extraAttributes(['style' => 'border-bottom:1px solid rgba(255,255,255,0.12); padding:2px 0;'])
                            ->schema([
                                TextEntry::make('created_label')->label('')->state('Created At:')->weight('bold')->alignment('right')->columnSpan(3),
                                TextEntry::make('created_value')->label('')->state(fn ($record) => $record->created_at?->format('Y-m-d H:i') ?: '—')->columnSpan(9),
                            ]),
                        InfoGrid::make(12)
                            ->extraAttributes(['style' => 'border-bottom:1px solid rgba(255,255,255,0.12); padding:2px 0;'])
                            ->schema([
                                TextEntry::make('updated_label')->label('')->state('Updated At:')->weight('bold')->alignment('right')->columnSpan(3),
                                TextEntry::make('updated_value')->label('')->state(fn ($record) => $record->updated_at?->format('Y-m-d H:i') ?: '—')->columnSpan(9),
                            ]),
                    ]),
            ])
            ->maxWidth('5xl')
            ->compact(), */
    ]);
}





    public static function table(Table $table): Table
    {
        return $table
            ->recordUrl(fn (Country $record) => static::getUrl('view', ['record' => $record]))
            ->columns([
                //
                TextColumn::make('id')
                    ->sortable(),
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('alpha_2')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('alpha_3')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('country_code')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('iso_code')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('am_best_code')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('latitude')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('longitude')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('region.name')
                ->label('Region')
                ->sortable(),


            ])
            ->filters([
                //
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    DeleteAction::make(),
                ])
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCountries::route('/'),
            'create' => CreateCountries::route('/create'),
            'view'   => ViewCountries::route('/{record}'),   // 👈 NUEVA
            'edit' => EditCountries::route('/{record}/edit'),
        ];
    }
}
