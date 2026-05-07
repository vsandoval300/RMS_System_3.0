<?php

namespace App\Filament\Resources\Coverages;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Actions\ActionGroup;
use Filament\Actions\ViewAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Resources\Coverages\Pages\ListCoverages;
use App\Filament\Resources\Coverages\Pages\CreateCoverages;
use App\Filament\Resources\Coverages\Pages\ViewCoverages;
use App\Filament\Resources\Coverages\Pages\EditCoverages;
use App\Filament\Resources\CoveragesResource\Pages;
use App\Filament\Resources\CoveragesResource\RelationManagers;
use App\Models\Coverage;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables\Grouping\Group;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Support\Enums\VerticalAlignment;
use Illuminate\Validation\Rules\Unique;
use Filament\Infolists\Components\TextEntry;


class CoveragesResource extends Resource
{
    protected static ?string $model = Coverage::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-minus';
    protected static string | \UnitEnum | null $navigationGroup = 'Underwritten';
    protected static ?int    $navigationSort  = 3;   // aparecerá primero

    /* ───── NUEVO: burbuja con el total en el menú ───── */
    public static function getNavigationBadge(): ?string
    {
        // Puedes usar self::$model::count() o Reinsurer::count()
        return Coverage::count();
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                //
                Section::make('Coverage Details')
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
                    ->maxLength(20)
                    ->rule('regex:/^[A-Z]+$/')
                    ->afterStateUpdated(fn ($state, callable $set) => $set('acronym', strtoupper($state)))
                    ->helperText('Only uppercase letters allowed.'),
                    //->extraAttributes(['class' => 'w-1/2']),

                    Textarea::make('description')
                    ->label('Description')
                    ->required()
                    ->columnSpan('full')
                    ->autosize()
                    ->afterStateUpdated(fn ($state, callable $set) => $set('description', ucfirst(strtolower($state))))
                    ->helperText('Please provide a brief description of the sector. Only the first letter will be capitalised.'),
                    //->extraAttributes(['class' => 'w-1/2']),

                    Select::make('lob_id')
                    ->label('Line of Business')
                    ->relationship('lineOfBusiness', 'name')
                    ->searchable()
                    ->required()
                    ->preload(),
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
            Section::make('Coverage Profile')->schema([
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
                                        TextEntry::make('name_label')->label('')->state('Name:')
                                            ->weight('bold')->alignment('right')->columnSpan(3),
                                        TextEntry::make('name_value')->label('')
                                            ->state(fn ($record) => $record->name ?: '—')
                                            ->columnSpan(9),
                                    ]),

                                // Acronym
                                \Filament\Schemas\Components\Grid::make(12)
                                    ->extraAttributes(['style' => 'border-bottom:1px solid rgba(255,255,255,0.12); padding:2px 0;'])
                                    ->schema([
                                        TextEntry::make('acr_label')->label('')->state('Acronym:')
                                            ->weight('bold')->alignment('right')->columnSpan(3),
                                        TextEntry::make('acr_value')->label('')
                                            ->state(fn ($record) => $record->acronym ?: '—')
                                            ->columnSpan(9),
                                    ]),

                                // Description (multilínea)
                                \Filament\Schemas\Components\Grid::make(12)
                                    ->extraAttributes(['style' => 'border-bottom:1px solid rgba(255,255,255,0.12); padding:2px 0;'])
                                    ->schema([
                                        TextEntry::make('desc_label')->label('')->state('Description:')
                                            ->weight('bold')->alignment('right')->columnSpan(3),
                                        TextEntry::make('desc_value')->label('')
                                            ->state(fn ($record) => $record->description ?: '—')
                                            ->extraAttributes(['style' => 'line-height:1.35;'])
                                            ->columnSpan(9),
                                    ]),

                                // Line of Business
                                \Filament\Schemas\Components\Grid::make(12)
                                    ->extraAttributes(['style' => 'border-bottom:1px solid rgba(255,255,255,0.12); padding:2px 0;'])
                                    ->schema([
                                        TextEntry::make('lob_label')->label('')->state('Line of Business:')
                                            ->weight('bold')->alignment('right')->columnSpan(3),
                                        TextEntry::make('lob_value')->label('')
                                            ->state(fn ($record) => $record->lineOfBusiness?->name ?: '—')
                                            ->columnSpan(9),
                                    ]),
                            ]),
                    ]),
            ])
            ->maxWidth('5xl')
            ->collapsible(),

            /* ─────────────────────────  AUDIT  ───────────────────────── */
            /* InfoSection::make('Audit Dates')->schema([
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
            ->maxWidth('5xl')
            ->compact(), */
        ]);
    }






    public static function table(Table $table): Table
    {
        return $table
            ->recordUrl(fn (Coverage $record) => static::getUrl('view', ['record' => $record]))
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
                TextColumn::make('description')
                    ->verticalAlignment(VerticalAlignment::Start)
                    ->label('Description')
                    ->sortable()
                    ->searchable()
                    ->wrap()
                    ->extraAttributes([
                        'style' => 'width: 400px; white-space: normal;', // ancho fijo de 300px
                    ]),
                TextColumn::make('lineOfBusiness.name')
                    ->verticalAlignment(VerticalAlignment::Start)
                    ->label('Line of Business')
                    ->sortable()
                    ->searchable()
                    ->extraAttributes([
                        'style' => 'width: 100px; white-space: normal;', // ancho fijo de 300px
                    ])
                
            ])
            ->defaultSort('lineOfBusiness.name','asc')
            //->defaultGroup('lineOfBusiness.name')
            ->groups([
                Group::make('lineOfBusiness.name')
                    ->label('Line of Business')
                    ->collapsible(), // 👈 clave para colapsar
            ])
            ->defaultGroup('lineOfBusiness.name') // 👈 activa el grupo automáticamente
            
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
            'index' => ListCoverages::route('/'),
            'create' => CreateCoverages::route('/create'),
            'view'   => ViewCoverages::route('/{record}'),   // 👈 NUEVA
            'edit' => EditCoverages::route('/{record}/edit'),
        ];
    }
}
