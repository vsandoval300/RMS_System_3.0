<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CurrenciesResource\Pages;
use App\Models\Currency;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Validation\Rules\Unique;

// 👇 IMPORTS para INFOLIST
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section as InfoSection;
use Filament\Infolists\Components\Grid as InfoGrid;
use Filament\Infolists\Components\TextEntry;


class CurrenciesResource extends Resource
{
    protected static ?string $model = Currency::class;
    protected static ?string $navigationIcon = 'heroicon-o-minus';
    protected static ?string $navigationGroup = 'Resources';
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
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make() //Grupo 1
                ->schema([
                    Forms\Components\Section::make('Currency Details')
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
    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            /* ─────────────────────────  PROFILE  ───────────────────────── */
            InfoSection::make('Currency Profile')->schema([
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
                                    ->extraAttributes([
                                        'style' => 'border-bottom:1px solid rgba(255,255,255,0.12); padding:2px 0;',
                                    ])
                                    ->schema([
                                        TextEntry::make('name_label')
                                            ->label('')
                                            ->state('Name:')
                                            ->weight('bold')
                                            ->alignment('right')
                                            ->columnSpan(3),
                                        TextEntry::make('name_value')
                                            ->label('')
                                            ->state(fn ($record) => $record->name ?: '—')
                                            ->columnSpan(9),
                                    ]),

                                // Acronym
                                InfoGrid::make(12)
                                    ->extraAttributes([
                                        'style' => 'border-bottom:1px solid rgba(255,255,255,0.12); padding:2px 0;',
                                    ])
                                    ->schema([
                                        TextEntry::make('acr_label')
                                            ->label('')
                                            ->state('Acronym:')
                                            ->weight('bold')
                                            ->alignment('right')
                                            ->columnSpan(3),
                                        TextEntry::make('acr_value')
                                            ->label('')
                                            ->state(fn ($record) => $record->acronym ?: '—')
                                            ->columnSpan(9),
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
                                    TextEntry::make('created_label')
                                        ->label('')->state('Created At:')->weight('bold')
                                        ->alignment('right')->columnSpan(3),
                                    TextEntry::make('created_value')
                                        ->label('')
                                        ->state(fn ($record) => $record->created_at?->format('Y-m-d H:i') ?: '—')
                                        ->columnSpan(9),
                                ]),

                            InfoGrid::make(12)
                                ->extraAttributes(['style' => 'border-bottom:1px solid rgba(255,255,255,0.12); padding:2px 0;'])
                                ->schema([
                                    TextEntry::make('updated_label')
                                        ->label('')->state('Updated At:')->weight('bold')
                                        ->alignment('right')->columnSpan(3),
                                    TextEntry::make('updated_value')
                                        ->label('')
                                        ->state(fn ($record) => $record->updated_at?->format('Y-m-d H:i') ?: '—')
                                        ->columnSpan(9),
                                ]),
                        ]),
                ])
                ->maxWidth('4xl')
                ->compact(), */
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
            'index' => Pages\ListCurrencies::route('/'),
            'create' => Pages\CreateCurrencies::route('/create'),
            'view'   => Pages\ViewCurrencies::route('/{record}'), 
            'edit' => Pages\EditCurrencies::route('/{record}/edit'),
        ];
    }
}
