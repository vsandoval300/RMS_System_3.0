<?php

namespace App\Filament\Resources\Producers;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Actions\ActionGroup;
use Filament\Actions\ViewAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Resources\Producers\Pages\ListProducers;
use App\Filament\Resources\Producers\Pages\CreateProducers;
use App\Filament\Resources\ProducersResource\Pages\ViewProducers;
use App\Filament\Resources\Producers\Pages\EditProducers;
use App\Filament\Resources\ProducersResource\Pages;
use App\Filament\Resources\ProducersResource\RelationManagers;
use App\Models\Producer;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Validation\Rules\Unique;
use Filament\Infolists\Components\TextEntry;

class ProducersResource extends Resource
{
    protected static ?string $model = Producer::class;
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-minus';
    protected static string | \UnitEnum | null $navigationGroup = 'Underwritten';
    protected static ?int    $navigationSort  = 14;   // aparecerá primero

    /* ───── NUEVO: burbuja con el total en el menú ───── */
    public static function getNavigationBadge(): ?string
    {
        return Producer::count();
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                //
                Section::make('Producer Details')
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

                ])
                ->maxWidth('5xl')
                ->collapsible(),
            ]);
    }




    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            /* ─────────────────────────  PROFILE  ───────────────────────── */
            Section::make('Producer Profile')
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
            ->recordUrl(fn (Producer $record) => static::getUrl('view', ['record' => $record]))
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
            'index' => ListProducers::route('/'),
            'create' => CreateProducers::route('/create'),
            'view'   => ViewProducers::route('/{record}'),   // 👈 NUEVA
            'edit' => EditProducers::route('/{record}/edit'),
        ];
    }
}
