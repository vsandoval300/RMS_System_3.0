<?php

namespace App\Filament\Resources\ReinsurerTypes;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Actions\ActionGroup;
use Filament\Actions\ViewAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Resources\ReinsurerTypes\Pages\ListReinsurerTypes;
use App\Filament\Resources\ReinsurerTypes\Pages\CreateReinsurerType;
use App\Filament\Resources\ReinsurerTypes\Pages\ViewReinsurerTypes;
use App\Filament\Resources\ReinsurerTypes\Pages\EditReinsurerType;
use App\Filament\Resources\ReinsurerTypeResource\Pages;
use App\Filament\Resources\ReinsurerTypeResource\RelationManagers;
use App\Models\ReinsurerType;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Grid;
use Filament\Tables\Columns\TextColumn;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rules\Unique;

class ReinsurerTypeResource extends Resource
{
    protected static ?string $model = ReinsurerType::class;
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-minus';
    protected static string | \UnitEnum | null $navigationGroup = 'Resources';
    protected static ?int    $navigationSort  = 8;   // aparecerá primero

    /* ───── NUEVO: burbuja con el total en el menú ───── */
    public static function getNavigationBadge(): ?string
    {
        return ReinsurerType::count();
    }


    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                //
                Section::make('Reinsurer Type Details')
                ->columns(1)    // ← aquí defines dos columnas
                ->columnSpanFull()
                ->schema([


                    TextInput::make('type_acronym')
                        ->label('Acronym')
                        ->placeholder('e.g. ABC')
                        ->required()
                        ->unique(
                            ignoreRecord: true,
                            modifyRuleUsing: fn (Unique $rule) => $rule->whereNull('deleted_at')
                        )
                        ->maxLength(2)                     // no deja escribir más de 3 caracteres
                        ->rule('regex:/^[A-Z]{2}$/')       // obliga a que sean EXACTAMENTE 3 letras A–Z
                        ->afterStateUpdated(fn ($state, callable $set) => $set('type_acronym', strtoupper($state)))
                        ->helperText('Only three uppercase letters allowed.')
                        ->extraAttributes(['class' => 'w-1/2']),    

                    TextInput::make('description')
                        ->label('Description')
                        ->required()
                        ->maxLength(255)
                        ->afterStateUpdated(fn ($state, callable $set) => $set('name', ucwords(strtolower($state))))
                        ->helperText('Please provide a brief description of the reinsurer type.')
                        ->extraAttributes(['class' => 'w-1/2']),
                        
                ]),


            ]);
    }


public static function infolist(Schema $schema): Schema
{
    return $schema->components([
        /* ─────────────────────────  PROFILE  ───────────────────────── */
        Section::make('Reinsurer Type Profile')
        ->columnSpanFull()
        ->schema([
            \Filament\Schemas\Components\Grid::make(1)
                ->extraAttributes(['style' => 'row-gap: 0;'])
                ->schema([
                    // Acronym
                    \Filament\Schemas\Components\Grid::make(12)
                        ->extraAttributes(['style' => 'border-bottom:1px solid rgba(255,255,255,0.12); padding:2px 0;'])
                        ->schema([
                            TextEntry::make('acr_label')
                                ->hiddenLabel()
                                ->state('Acronym:')
                                ->weight('bold')
                                ->alignment('right')
                                ->columnSpan(3),
                            TextEntry::make('acr_value')
                                ->hiddenLabel()
                                ->state(fn ($record) => $record->type_acronym ? strtoupper($record->type_acronym) : '—')
                                ->columnSpan(9),
                        ]),

                    // Description
                    \Filament\Schemas\Components\Grid::make(12)
                        ->extraAttributes(['style' => 'border-bottom:1px solid rgba(255,255,255,0.12); padding:2px 0;'])
                        ->schema([
                            TextEntry::make('desc_label')
                                ->hiddenLabel()
                                ->state('Description:')
                                ->weight('bold')
                                ->alignment('right')
                                ->columnSpan(3),
                            TextEntry::make('desc_value')
                                ->hiddenLabel()
                                ->state(fn ($record) => $record->description ?: '—')
                                ->columnSpan(9),
                        ]),
                ]),
        ])
        ->maxWidth('5xl')
        ->collapsible(),

        /* ─────────────────────────  AUDIT  ───────────────────────── */
        /* InfoSection::make('Audit Dates')->schema([
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
        ])
        ->maxWidth('5xl')
        ->compact(), */
    ]);
}







    public static function table(Table $table): Table
    {
        return $table
            ->recordUrl(fn (ReinsurerType $record) => static::getUrl('view', ['record' => $record]))
            ->columns([
                //
                TextColumn::make('id')
                    ->sortable(),
                TextColumn::make('type_acronym')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('description')
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
            'index' => ListReinsurerTypes::route('/'),
            'create' => CreateReinsurerType::route('/create'),
            'view'   => ViewReinsurerTypes::route('/{record}'),   // 👈 NUEVA
            'edit' => EditReinsurerType::route('/{record}/edit'),
        ];
    }
}
