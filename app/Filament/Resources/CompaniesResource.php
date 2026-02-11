<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CompaniesResource\Pages;
use App\Models\Company;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use App\Models\Country;
use Filament\Support\Enums\VerticalAlignment;
use Illuminate\Validation\Rules\Unique;



// 👇 IMPORTS para INFOLIST
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section as InfoSection;
use Filament\Infolists\Components\Grid as InfoGrid;
use Filament\Infolists\Components\TextEntry;

class CompaniesResource extends Resource
{
    protected static ?string $model = Company::class;
    protected static ?string $navigationIcon = 'heroicon-o-minus';
    protected static ?string $navigationGroup = 'Underwritten';
    protected static ?int    $navigationSort  = 2;   // aparecerá primero

    /* ───── NUEVO: burbuja con el total en el menú ───── */
    public static function getNavigationBadge(): ?string
    {
        // Puedes usar self::$model::count() o Reinsurer::count()
        return Company::count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
                Section::make('Companies Details')
                ->columns(1)    // ← aquí defines dos columnas
                ->schema([
                    

                    TextInput::make('name')
                    ->label('Name')
                    ->required()
                    ->unique(
                        ignoreRecord: true,
                        modifyRuleUsing: fn (Unique $rule) => $rule->whereNull('deleted_at')
                    )
                    ->maxLength(255)
                    ->afterStateUpdated(fn ($state, callable $set) => $set('name', ucwords(strtolower($state))))
                    ->helperText('First letter of each word will be capitalised.'),
                    //->extraAttributes(['class' => 'w-1/2']),

                    TextInput::make('acronym')
                    ->label('Acronym')
                    ->required()
                    ->unique(
                        ignoreRecord: true,
                        modifyRuleUsing: fn (Unique $rule) => $rule->whereNull('deleted_at')
                    )
                    //->live(onBlur: false)
                    ->maxLength(255)
                    ->rule('regex:/^[A-Z_]+$/')
                    ->afterStateUpdated(fn ($state, callable $set) => $set('acronym', strtoupper($state)))
                    ->helperText('Only uppercase letters allowed.'),
                    //->extraAttributes(['class' => 'w-1/2']),

                    Textarea::make('activity')
                    ->label('Activity')
                    ->required()
                    ->columnSpan('full')
                    ->autosize()
                    ->afterStateUpdated(fn ($state, callable $set) => $set('description', ucfirst(strtolower($state))))
                    ->helperText('Please provide a brief description of the sector. Only the first letter will be capitalised.'),
                    //->extraAttributes(['class' => 'w-1/2']),

                    Select::make('country_id')
                            ->label(__('Country'))
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
                            ->placeholder('Choose the reinsurer\'s country')
                            ->required()
                            ->placeholder('Select a country'),
                            //->helperText('Choose the reinsurer\'s country.'),

                    Select::make('industry_id')
                        ->label('Industry')
                        ->relationship('sector','name')
                        ->searchable()
                        ->preload()
                        ->required(),
                        //->extraAttributes(['class' => 'w-1/2']),

                ])
                ->maxWidth('5xl')
                ->collapsible(),

            ]);
    }






    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            /* ─────────────────────────  PROFILE  ───────────────────────── */
            InfoSection::make('Company Profile')->schema([
                InfoGrid::make(2)
                    ->extraAttributes(['style' => 'gap: 6px;'])
                    ->schema([

                        // Cols 1–2: filas “Label (3) + Value (9)”
                        InfoGrid::make(1)
                            ->columnSpan(2)
                            ->extraAttributes(['style' => 'row-gap: 0;'])
                            ->schema([

                                // Name
                                InfoGrid::make(12)
                                    ->extraAttributes(['style' => 'border-bottom:1px solid rgba(255,255,255,0.12); padding:2px 0;'])
                                    ->schema([
                                        TextEntry::make('name_label')->label('')->state('Name:')
                                            ->weight('bold')->alignment('right')->columnSpan(3),
                                        TextEntry::make('name_value')->label('')
                                            ->state(fn ($record) => $record->name ?: '—')
                                            ->columnSpan(9),
                                    ]),

                                // Acronym
                                InfoGrid::make(12)
                                    ->extraAttributes(['style' => 'border-bottom:1px solid rgba(255,255,255,0.12); padding:2px 0;'])
                                    ->schema([
                                        TextEntry::make('acr_label')->label('')->state('Acronym:')
                                            ->weight('bold')->alignment('right')->columnSpan(3),
                                        TextEntry::make('acr_value')->label('')
                                            ->state(fn ($record) => $record->acronym ?: '—')
                                            ->columnSpan(9),
                                    ]),

                                // Activity (multilínea)
                                InfoGrid::make(12)
                                    ->extraAttributes(['style' => 'border-bottom:1px solid rgba(255,255,255,0.12); padding:2px 0;'])
                                    ->schema([
                                        TextEntry::make('act_label')->label('')->state('Activity:')
                                            ->weight('bold')->alignment('right')->columnSpan(3),
                                        TextEntry::make('act_value')->label('')
                                            ->state(fn ($record) => $record->activity ?: '—')
                                            ->extraAttributes(['style' => 'line-height:1.35;'])
                                            ->columnSpan(9),
                                    ]),

                                // Country
                                InfoGrid::make(12)
                                    ->extraAttributes(['style' => 'border-bottom:1px solid rgba(255,255,255,0.12); padding:2px 0;'])
                                    ->schema([
                                        TextEntry::make('country_label')->label('')->state('Country:')
                                            ->weight('bold')->alignment('right')->columnSpan(3),
                                        TextEntry::make('country_value')->label('')
                                            ->state(fn ($record) =>
                                                $record->country
                                                    ? ($record->country->alpha_3 ?? '') . (isset($record->country->alpha_3) ? ' - ' : '') . $record->country->name
                                                    : '—'
                                            )
                                            ->columnSpan(9),
                                    ]),

                                // Sector
                                InfoGrid::make(12)
                                    ->extraAttributes(['style' => 'border-bottom:1px solid rgba(255,255,255,0.12); padding:2px 0;'])
                                    ->schema([
                                        TextEntry::make('sector_label')->label('')->state('Sector:')
                                            ->weight('bold')->alignment('right')->columnSpan(3),
                                        TextEntry::make('sector_value')->label('')
                                            ->state(fn ($record) => $record->sector?->name ?: '—')
                                            ->columnSpan(9),
                                    ]),
                            ]),
                    ]),
            ])
            ->maxWidth('5xl')
            ->collapsible(),

           
        ]);
    }





    public static function table(Table $table): Table
    {
        return $table
            ->recordUrl(fn (Company $record) => static::getUrl('view', ['record' => $record]))
            ->columns([
                //
                TextColumn::make('id')
                    ->verticalAlignment(VerticalAlignment::Start)
                    ->sortable(),
                TextColumn::make('name')
                    ->verticalAlignment(VerticalAlignment::Start)
                    ->searchable()
                    ->sortable()
                    ->extraAttributes([
                        'style' => 'width: 320px; white-space: normal;', // ✅ Deja que el texto se envuelva
                    ]),
                TextColumn::make('acronym')
                    ->verticalAlignment(VerticalAlignment::Start)
                    ->searchable()
                    ->sortable(),
                TextColumn::make('activity')
                    ->verticalAlignment(VerticalAlignment::Start)
                    ->label('Activity')
                    ->sortable()
                    ->searchable()
                    ->wrap()
                    ->extraAttributes([
                        'style' => 'width: 550px; white-space: normal;', // ancho fijo de 300px
                    ]),
                TextColumn::make('sector.name')
                    ->verticalAlignment(VerticalAlignment::Start)
                    ->label('Sector')
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('country.name')
                    ->verticalAlignment(VerticalAlignment::Start)
                    ->label('Country')
                    ->sortable()
                    ->searchable()
                    ->extraAttributes([
                        'style' => 'width: 250px; white-space: normal;', // ancho fijo de 300px
                    ]),


            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListCompanies::route('/'),
            'create' => Pages\CreateCompanies::route('/create'),
            'view'   => Pages\ViewCompanies::route('/{record}'),   // 👈 NUEVA
            'edit' => Pages\EditCompanies::route('/{record}/edit'),
        ];
    }
}
