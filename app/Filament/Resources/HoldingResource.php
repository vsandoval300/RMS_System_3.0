<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HoldingResource\Pages;
use App\Filament\Resources\HoldingResource\RelationManagers;
use App\Models\Holding;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\Select;
use App\Models\Country;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Section;
use Illuminate\Validation\Rules\Unique;

// 👇 IMPORTS para INFOLIST
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section as InfoSection;
use Filament\Infolists\Components\Grid as InfoGrid;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;

class HoldingResource extends Resource
{
    protected static ?string $model = Holding::class;

    protected static ?string $navigationIcon = 'heroicon-o-minus';
    protected static ?string $navigationGroup = 'Compliance';
    protected static ?int    $navigationSort  = 1;   // aparecerá primero

    /* ───── NUEVO: burbuja con el total en el menú ───── */
    public static function getNavigationBadge(): ?string
    {
        // Puedes usar self::$model::count() o Reinsurer::count()
        return Holding::count();
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make('Holding Profile')
                    ->compact()
                    ->schema([
                    Forms\Components\Grid::make(2)->schema([            
                        TextInput::make('name')
                            ->required()
                            ->unique(
                                ignoreRecord: true,
                                modifyRuleUsing: fn (Unique $rule) => $rule->whereNull('deleted_at')
                            )
                            ->maxLength(400),
                        TextInput::make('short_name')
                            ->required()
                            ->unique(
                                ignoreRecord: true,
                                modifyRuleUsing: fn (Unique $rule) => $rule->whereNull('deleted_at')
                            )
                            ->maxLength(60),
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

                        Select::make('client_id')
                            ->relationship('client', 'name')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->optionsLimit(300),
                        ]),
                    ]),
        ]);
    }






    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            /* ─────────────────────────  PROFILE  ───────────────────────── */
            InfoSection::make('Holding Profile')->schema([
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

                                // Client
                                InfoGrid::make(12)
                                    ->extraAttributes(['style' => 'border-bottom:1px solid rgba(255,255,255,0.12); padding:2px 0;'])
                                    ->schema([
                                        TextEntry::make('client_label')->label('')->state('Client:')
                                            ->weight('bold')->alignment('right')->columnSpan(3),
                                        TextEntry::make('client_value')->label('')
                                            ->state(fn ($record) => $record->client?->name ?: '—')
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
                            // Created at
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

                            // Updated at
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
                ->maxWidth('5xl')
                ->compact(), */
        ]);
    }

















    public static function table(Table $table): Table
    {
        return $table
            ->recordUrl(fn (Holding $record) => static::getUrl('view', ['record' => $record]))
            ->columns([
                TextColumn::make('Index')
                    ->label('Index')
                    ->state(fn ($record, $rowLoop) => $rowLoop->iteration)
                    ->sortable(false) // 👈 no tiene sentido ordenar este índice
                    ->searchable(false), // 👈 tampoco buscarlo

                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('short_name')
                    ->searchable(),
                TextColumn::make('country.name')
                    ->numeric()
                    ->sortable()
                    ->searchable(),
                TextColumn::make('client.name')
                    ->numeric()
                    ->sortable()
                    ->searchable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
            'index' => Pages\ListHoldings::route('/'),
            'create' => Pages\CreateHolding::route('/create'),
            'view'   => Pages\ViewHolding::route('/{record}'),  // 👈 nuevo
            'edit' => Pages\EditHolding::route('/{record}/edit'),
        ];
    }
}
