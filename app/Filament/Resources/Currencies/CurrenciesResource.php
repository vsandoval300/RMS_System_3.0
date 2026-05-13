<?php

namespace App\Filament\Resources\Currencies;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Actions\ActionGroup;
use Filament\Actions\ViewAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Resources\Currencies\Pages\ListCurrencies;
use App\Filament\Resources\Currencies\Pages\CreateCurrencies;
use App\Filament\Resources\Currencies\Pages\ViewCurrencies;
use App\Filament\Resources\Currencies\Pages\EditCurrencies;
use App\Filament\Resources\CurrenciesResource\Pages;
use App\Models\Currency;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Validation\Rules\Unique;
use Filament\Infolists\Components\TextEntry;


class CurrenciesResource extends Resource
{
    protected static ?string $model = Currency::class;
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-minus';
    protected static string | \UnitEnum | null $navigationGroup = 'Resources';
    protected static ?int    $navigationSort  = 1;   // aparecerá primero

    /* ───── NUEVO: burbuja con el total en el menú ───── */
    public static function getNavigationBadge(): ?string
    {
        // Puedes usar self::$model::count() o Reinsurer::count()
        return Currency::count();
    }

    /* public static function canCreate(): bool
    {
        // Devuelve false para ocultar el botón “New country”
        return false;
    } */


    /*--------------------------------------------------------------
     | 1. Form New and Edit
     --------------------------------------------------------------*/
    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make() //Grupo 1
                ->schema([
                    Section::make('Currency Details')
                    ->schema([
                    
                        TextInput::make('name')
                            ->label('Name')
                            ->placeholder('Please provide name')
                            ->required()
                            ->unique(
                                ignoreRecord: true,
                                modifyRuleUsing: fn (Unique $rule) => $rule->whereNull('deleted_at')
                            )
                            ->maxLength(255)
                            ->afterStateUpdated(fn ($state, callable $set) => $set('name', ucwords(strtolower($state)))),
                            /* ->helperText(fn (string $context) => in_array($context, ['create', 'edit']) 
                                ? 'First letter of each word will be capitalised.' 
                                : null), */
                            
                        TextInput::make('acronym')
                            ->label('Acronym')
                            ->placeholder('e.g. ABC')
                            ->required()
                            ->unique(
                                ignoreRecord: true,
                                modifyRuleUsing: fn (Unique $rule) => $rule->whereNull('deleted_at')
                            )
                            ->maxLength(3)                     // no deja escribir más de 3 caracteres
                            ->rule('regex:/^[A-Z]{3}$/')       // obliga a que sean EXACTAMENTE 3 letras A–Z
                            ->afterStateUpdated(fn ($state, callable $set) => $set('acronym', strtoupper($state)))
                            ->helperText('Only three uppercase letters allowed.')
                            ->columnSpan(1),

                            
                        
                               
                    ])
                    ->columns(2),
                ])
                ->columnSpanFull(),
            ]);
    }



    /*--------------------------------------------------------------
     | 2. Infolist
     --------------------------------------------------------------*/
    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            /* ─────────────────────────  PROFILE  ───────────────────────── */
            Section::make('Currency Profile')
            ->columnSpan('full')
            ->schema([
                Grid::make(2)
                    ->extraAttributes(['style' => 'gap: 6px;'])
                    ->schema([

                        // Cols 1–2: filas “Label (3) + Value (9)”
                        Grid::make(1)
                            ->columnSpan(2)
                            ->extraAttributes(['style' => 'row-gap: 0;'])
                            ->schema([

                                // Name
                                Grid::make(12)
                                    ->extraAttributes([
                                        'style' => 'border-bottom:1px solid rgba(255,255,255,0.12); padding:2px 0;',
                                    ])
                                    ->schema([
                                        TextEntry::make('name_label')
                                            ->hiddenLabel()
                                            ->state('Name:')
                                            ->weight('bold')
                                            ->alignment('right')
                                            ->columnSpan(3),
                                        TextEntry::make('name_value')
                                            ->hiddenLabel()
                                            ->state(fn ($record) => $record->name ?: '—')
                                            ->columnSpan(9),
                                    ]),

                                // Acronym
                                Grid::make(12)
                                    ->extraAttributes([
                                        'style' => 'border-bottom:1px solid rgba(255,255,255,0.12); padding:2px 0;',
                                    ])
                                    ->schema([
                                        TextEntry::make('acr_label')
                                            ->hiddenLabel()
                                            ->state('Acronym:')
                                            ->weight('bold')
                                            ->alignment('right')
                                            ->columnSpan(3),
                                        TextEntry::make('acr_value')
                                            ->hiddenLabel()
                                            ->state(fn ($record) => $record->acronym ?: '—')
                                            ->columnSpan(9),
                                    ]),
                            ]),

                        
                    ]),
            ])
            ->maxWidth('5xl')
            ->collapsible(),
        ]);
    }



    /*--------------------------------------------------------------
     | 3. CRUD Table
     --------------------------------------------------------------*/
    public static function table(Table $table): Table
    {
        return $table
            ->recordUrl(fn (Currency $record) => static::getUrl('view', ['record' => $record]))
            ->columns([
                //
                TextColumn::make('id')
                    ->sortable(),
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('acronym')
                    ->searchable()
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
            'index' => ListCurrencies::route('/'),
            'create' => CreateCurrencies::route('/create'),
            'view'   => ViewCurrencies::route('/{record}'), 
            'edit' => EditCurrencies::route('/{record}/edit'),
        ];
    }
}
