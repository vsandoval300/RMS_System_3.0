<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProducersResource\Pages;
use App\Filament\Resources\ProducersResource\RelationManagers;
use App\Models\Producer;
use Filament\Forms;
use Filament\Forms\Form;
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

class ProducersResource extends Resource
{
    protected static ?string $model = Producer::class;
    protected static ?string $navigationIcon = 'heroicon-o-minus';
    protected static ?string $navigationGroup = 'Underwritten';
    protected static ?int    $navigationSort  = 6;   // aparecerá primero

    /* ───── NUEVO: burbuja con el total en el menú ───── */
    public static function getNavigationBadge(): ?string
    {
        return Producer::count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
                Section::make('Producer Details')
                ->columns(1)    // ← aquí defines dos columnas
                ->schema([

                    TextInput::make('name')
                    ->label('Name')
                    ->required()
                    ->unique()
                    ->maxLength(255)
                    ->afterStateUpdated(fn ($state, callable $set) => $set('name', ucwords(strtolower($state))))
                    ->helperText('First letter of each word will be capitalised.')
                    ->extraAttributes(['class' => 'w-1/2']),

                    TextInput::make('acronym')
                    ->label('Acronym')
                    ->required()
                    ->unique()
                    ->live(onBlur: false)
                    ->maxLength(3)
                    ->rule('regex:/^[A-Z]+$/')
                    ->afterStateUpdated(fn ($state, callable $set) => $set('acronym', strtoupper($state)))
                    ->helperText('Only uppercase letters allowed.')
                    ->extraAttributes(['class' => 'w-1/2']),

                ]),
            ]);
    }




    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            /* ─────────────────────────  PROFILE  ───────────────────────── */
            InfoSection::make('Producer Profile')->schema([
                InfoGrid::make(2)
                    ->extraAttributes(['style' => 'gap: 6px;'])
                    ->schema([
                        // Filas “Label (3) + Value (9)”
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
                            ]),
                    ]),
            ])
            ->maxWidth('5xl')
            ->collapsible(),

            /* ─────────────────────────  AUDIT  ───────────────────────── */
            InfoSection::make('Audit Dates')->schema([
                InfoGrid::make(2)
                    ->extraAttributes(['style' => 'gap: 12px;'])
                    ->schema([
                        // Created at
                        InfoGrid::make(12)
                            ->extraAttributes(['style' => 'border-bottom:1px solid rgba(255,255,255,0.12); padding:2px 0;'])
                            ->schema([
                                TextEntry::make('created_label')->label('')->state('Created At:')
                                    ->weight('bold')->alignment('right')->columnSpan(3),
                                TextEntry::make('created_value')->label('')
                                    ->state(fn ($record) => $record->created_at?->format('Y-m-d H:i') ?: '—')
                                    ->columnSpan(9),
                            ]),
                        // Updated at
                        InfoGrid::make(12)
                            ->extraAttributes(['style' => 'border-bottom:1px solid rgba(255,255,255,0.12); padding:2px 0;'])
                            ->schema([
                                TextEntry::make('updated_label')->label('')->state('Updated At:')
                                    ->weight('bold')->alignment('right')->columnSpan(3),
                                TextEntry::make('updated_value')->label('')
                                    ->state(fn ($record) => $record->updated_at?->format('Y-m-d H:i') ?: '—')
                                    ->columnSpan(9),
                            ]),
                    ]),
            ])
            ->maxWidth('5xl')
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
            'index' => Pages\ListProducers::route('/'),
            'create' => Pages\CreateProducers::route('/create'),
            'edit' => Pages\EditProducers::route('/{record}/edit'),
        ];
    }
}
