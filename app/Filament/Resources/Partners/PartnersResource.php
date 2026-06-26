<?php

namespace App\Filament\Resources\Partners;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Actions\ActionGroup;
use Filament\Actions\ViewAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Resources\Partners\Pages\ListPartners;
use App\Filament\Resources\Partners\Pages\CreatePartners;
use App\Filament\Resources\Partners\Pages\ViewPartners;
use App\Filament\Resources\Partners\Pages\EditPartners;
use App\Filament\Resources\PartnersResource\Pages;
use App\Filament\Resources\PartnersResource\RelationManagers;
use App\Models\Country;
use App\Models\Partner;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Grid;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Validation\Rules\Unique;
use Filament\Infolists\Components\TextEntry;


class PartnersResource extends Resource
{
    protected static ?string $model = Partner::class;
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-minus';
    protected static string | \UnitEnum | null $navigationGroup = 'Underwritten';
    protected static ?int    $navigationSort  = 5;   // aparecerá primero

     /* ───── NUEVO: burbuja con el total en el menú ───── */
    public static function getNavigationBadge(): ?string
    {
        // Puedes usar self::$model::count() o Reinsurer::count()
        return Partner::count();
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                //
                Section::make('Partners Details')
                ->columns(1)    // ← aquí defines dos columnas
                ->columnSpanFull()
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

                    TextInput::make('short_name')
                        ->label('Short Name')
                        ->required()
                        ->unique(
                            ignoreRecord: true,
                            modifyRuleUsing: fn (Unique $rule) => $rule->whereNull('deleted_at')
                        )
                        //->live(onBlur: false)
                        ->maxLength(255)
                        ->afterStateUpdated(fn ($state, callable $set) => $set('short_name', strtoupper($state)))
                        ->helperText('Only uppercase letters allowed.'),
                        //->extraAttributes(['class' => 'w-1/2']),

                    TextInput::make('acronym')
                        ->label('Acronym')
                        ->required()
                        ->unique(
                            ignoreRecord: true,
                            modifyRuleUsing: fn (Unique $rule) => $rule->whereNull('deleted_at')
                        )
                        //->live(onBlur: false)
                        ->maxLength(3)
                        ->rule('regex:/^[A-Z]+$/')
                        ->afterStateUpdated(fn ($state, callable $set) => $set('acronym', strtoupper($state)))
                        ->helperText('Only uppercase letters allowed.'),
                        //->extraAttributes(['class' => 'w-1/2']),

                    Select::make('partner_types_id')
                        ->label('Partner Type')
                        ->relationship('partnerType', 'name') // relación del modelo + campo visible
                        ->searchable()
                        ->required()
                        ->preload(),
                        //->extraAttributes(['class' => 'w-1/2']),

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
                        ->optionsLimit(300)
                        ->required()
                        ->placeholder('Select a country')
                        ->helperText('Choose the reinsurer\'s country.'),
                        //->extraAttributes(['class' => 'w-1/2']),

                ])
                ->maxWidth('5xl')
                ->collapsible(),
            ]);
    }





    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            /* ─────────────────────────  PROFILE  ───────────────────────── */
            Section::make('Partner Profile')
            ->columnSpanFull()
            ->schema([
                \Filament\Schemas\Components\Grid::make(2)
                    ->extraAttributes(['style' => 'gap: 6px;'])
                    ->schema([
                        // Filas “Label (3) + Value (9)”
                        \Filament\Schemas\Components\Grid::make(1)
                            ->columnSpan(2)
                            ->extraAttributes(['style' => 'row-gap: 0;'])
                            ->schema([

                                // Name
                                \Filament\Schemas\Components\Grid::make(12)
                                    ->extraAttributes(['style' => 'border-bottom:1px solid rgba(255,255,255,0.12); padding:2px 0;'])
                                    ->schema([
                                        TextEntry::make('name_label')->hiddenLabel()->state('Name:')
                                            ->weight('bold')->alignment('right')->columnSpan(3),
                                        TextEntry::make('name_value')->hiddenLabel()
                                            ->state(fn ($record) => $record->name ?: '—')
                                            ->columnSpan(9),
                                    ]),

                                // Short Name
                                \Filament\Schemas\Components\Grid::make(12)
                                    ->extraAttributes(['style' => 'border-bottom:1px solid rgba(255,255,255,0.12); padding:2px 0;'])
                                    ->schema([
                                        TextEntry::make('short_label')->hiddenLabel()->state('Short Name:')
                                            ->weight('bold')->alignment('right')->columnSpan(3),
                                        TextEntry::make('short_value')->hiddenLabel()
                                            ->state(fn ($record) => $record->short_name ?: '—')
                                            ->columnSpan(9),
                                    ]),

                                // Acronym
                                \Filament\Schemas\Components\Grid::make(12)
                                    ->extraAttributes(['style' => 'border-bottom:1px solid rgba(255,255,255,0.12); padding:2px 0;'])
                                    ->schema([
                                        TextEntry::make('acr_label')->hiddenLabel()->state('Acronym:')
                                            ->weight('bold')->alignment('right')->columnSpan(3),
                                        TextEntry::make('acr_value')->hiddenLabel()
                                            ->state(fn ($record) => $record->acronym ?: '—')
                                            ->columnSpan(9),
                                    ]),

                                // Partner Type
                                \Filament\Schemas\Components\Grid::make(12)
                                    ->extraAttributes(['style' => 'border-bottom:1px solid rgba(255,255,255,0.12); padding:2px 0;'])
                                    ->schema([
                                        TextEntry::make('ptype_label')->hiddenLabel()->state('Partner Type:')
                                            ->weight('bold')->alignment('right')->columnSpan(3),
                                        TextEntry::make('ptype_value')->hiddenLabel()
                                            ->state(fn ($record) => $record->partnerType?->name ?: '—')
                                            ->columnSpan(9),
                                    ]),

                                // Country
                                \Filament\Schemas\Components\Grid::make(12)
                                    ->extraAttributes(['style' => 'border-bottom:1px solid rgba(255,255,255,0.12); padding:2px 0;'])
                                    ->schema([
                                        TextEntry::make('country_label')->hiddenLabel()->state('Country:')
                                            ->weight('bold')->alignment('right')->columnSpan(3),
                                        TextEntry::make('country_value')->hiddenLabel()
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
            ->maxWidth('5xl')
            ->collapsible(),
        ]);
    }









    public static function table(Table $table): Table
    {
        return $table
            ->recordUrl(fn (Partner $record) => static::getUrl('view', ['record' => $record]))
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
            'index' => ListPartners::route('/'),
            'create' => CreatePartners::route('/create'),
            'view'   => ViewPartners::route('/{record}'),   // 👈 NUEVA
            'edit' => EditPartners::route('/{record}/edit'),
        ];
    }
}
