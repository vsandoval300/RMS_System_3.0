<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CurrenciesResource\Pages;
use App\Filament\Resources\CurrenciesResource\RelationManagers;
use App\Models\Currency;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\FormsComponent;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;
use Filament\Tables\Columns\TextColumn;

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

    public static function canCreate(): bool
    {
        // Devuelve false para ocultar el botón “New country”
        return false;
    }

   
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
            ->maxWidth('4xl')
            ->collapsible(),

            /* ─────────────────────────  AUDIT  ───────────────────────── */
            InfoSection::make('Audit Dates')
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
                ->compact(),
        ]);
    }



























    public static function table(Table $table): Table
    {
        return $table
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
                Tables\Actions\ViewAction::make(),   // 👈 sustituto de Edit
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
            //'create' => Pages\CreateCurrencies::route('/create'),
            //'edit' => Pages\EditCurrencies::route('/{record}/edit'),
        ];
    }
}
