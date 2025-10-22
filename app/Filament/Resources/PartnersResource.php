<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PartnersResource\Pages;
use App\Filament\Resources\PartnersResource\RelationManagers;
use App\Models\Country;
use App\Models\Partner;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;
use Filament\Tables\Columns\TextColumn;


// 👇 IMPORTS para INFOLIST
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section as InfoSection;
use Filament\Infolists\Components\Grid as InfoGrid;
use Filament\Infolists\Components\TextEntry;


class PartnersResource extends Resource
{
    protected static ?string $model = Partner::class;
    protected static ?string $navigationIcon = 'heroicon-o-minus';
    protected static ?string $navigationGroup = 'Underwritten';
    protected static ?int    $navigationSort  = 5;   // aparecerá primero

     /* ───── NUEVO: burbuja con el total en el menú ───── */
    public static function getNavigationBadge(): ?string
    {
        // Puedes usar self::$model::count() o Reinsurer::count()
        return Partner::count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
                Section::make('Partners Details')
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

                    TextInput::make('short_name')
                    ->label('Short Name')
                    ->required()
                    ->unique()
                    ->live(onBlur: false)
                    ->maxLength(255)
                    ->afterStateUpdated(fn ($state, callable $set) => $set('short_name', strtoupper($state)))
                    ->helperText('Only uppercase letters allowed.')
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

                    Select::make('partner_types_id')
                    ->label('Partner Type')
                    ->relationship('partnerType', 'name') // relación del modelo + campo visible
                    ->searchable()
                    ->required()
                    ->preload()
                    ->extraAttributes(['class' => 'w-1/2']),

                    Select::make('country_id')
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
                    ->required()
                    ->placeholder('Select a country')
                    ->helperText('Choose the reinsurer\'s country.')
                    ->extraAttributes(['class' => 'w-1/2']),

                ]),
            ]);
    }





    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            /* ─────────────────────────  PROFILE  ───────────────────────── */
            InfoSection::make('Partner Profile')->schema([
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

                                // Short Name
                                InfoGrid::make(12)
                                    ->extraAttributes(['style' => 'border-bottom:1px solid rgba(255,255,255,0.12); padding:2px 0;'])
                                    ->schema([
                                        TextEntry::make('short_label')->label('')->state('Short Name:')
                                            ->weight('bold')->alignment('right')->columnSpan(3),
                                        TextEntry::make('short_value')->label('')
                                            ->state(fn ($record) => $record->short_name ?: '—')
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

                                // Partner Type
                                InfoGrid::make(12)
                                    ->extraAttributes(['style' => 'border-bottom:1px solid rgba(255,255,255,0.12); padding:2px 0;'])
                                    ->schema([
                                        TextEntry::make('ptype_label')->label('')->state('Partner Type:')
                                            ->weight('bold')->alignment('right')->columnSpan(3),
                                        TextEntry::make('ptype_value')->label('')
                                            ->state(fn ($record) => $record->partnerType?->name ?: '—')
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
                                                    ? "{$record->country->alpha_3} - {$record->country->name}"
                                                    : '—'
                                            )
                                            ->columnSpan(9),
                                    ]),
                            ]),
                    ]),
            ])
            ->maxWidth('6xl')
            ->collapsible(),

            /* ─────────────────────────  AUDIT  ───────────────────────── */
            InfoSection::make('Audit Dates')->schema([
                InfoGrid::make(2)
                    ->extraAttributes(['style' => 'gap: 12px;'])
                    ->schema([
                        InfoGrid::make(12)
                            ->extraAttributes(['style' => 'border-bottom:1px solid rgba(255,255,255,0.12); padding:2px 0;'])
                            ->schema([
                                TextEntry::make('created_label')->label('')->state('Created At:')
                                    ->weight('bold')->alignment('right')->columnSpan(3),
                                TextEntry::make('created_value')->label('')
                                    ->state(fn ($record) => $record->created_at?->format('Y-m-d H:i') ?: '—')
                                    ->columnSpan(9),
                            ]),
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
            ->maxWidth('6xl')
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
                    ->sortable()
                    ->extraAttributes([
                        'style' => 'width: 320px; white-space: normal;', // ✅ Deja que el texto se envuelva
                    ]),
                TextColumn::make('short_name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('acronym')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('partnerType.name')
                    ->label('Partner Type')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('country.name')
                    ->label('Country')
                    ->sortable()
                    ->searchable(),

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
            'index' => Pages\ListPartners::route('/'),
            'create' => Pages\CreatePartners::route('/create'),
            'edit' => Pages\EditPartners::route('/{record}/edit'),
        ];
    }
}
