<?php

namespace App\Filament\Resources\OperativeStatuses;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Actions\ActionGroup;
use Filament\Actions\ViewAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Resources\OperativeStatuses\Pages\ListOperativeStatuses;
use App\Filament\Resources\OperativeStatuses\Pages\CreateOperativeStatuses;
use App\Filament\Resources\OperativeStatuses\Pages\ViewOperativeStatuses;
use App\Filament\Resources\OperativeStatuses\Pages\EditOperativeStatuses;
use App\Filament\Resources\OperativeStatusesResource\Pages;
use App\Filament\Resources\OperativeStatusesResource\RelationManagers;
use App\Models\OperativeStatus;
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
use Filament\Forms\Components\Textarea;
use Illuminate\Validation\Rules\Unique;
use Filament\Infolists\Components\TextEntry;

class OperativeStatusesResource extends Resource
{
    protected static ?string $model = OperativeStatus::class;
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-minus';
    protected static string | \UnitEnum | null $navigationGroup = 'Resources';
    protected static ?int    $navigationSort  = 6;   // aparecerá primero

    /* ───── NUEVO: burbuja con el total en el menú ───── */
    public static function getNavigationBadge(): ?string
    {
        // Puedes usar self::$model::count() o Reinsurer::count()
        return OperativeStatus::count();
    }

    public static function canCreate(): bool
    {
        // Devuelve false para ocultar el botón “New country”
        return true;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                //
                Section::make('Operative Status Details')
                ->columns(1)    // ← aquí defines dos columnas
                ->columnSpanFull()
                ->schema([

                    TextInput::make('acronym')
                        ->label('Acronym')
                        ->required()
                        ->unique(
                            ignoreRecord: true,
                            modifyRuleUsing: fn (Unique $rule) => $rule->whereNull('deleted_at')
                        )
                        ->maxLength(2)
                        ->rule('regex:/^[A-Z]+$/')
                        ->afterStateUpdated(fn ($state, callable $set) => $set('acronym', strtoupper($state)))
                        ->extraAttributes(['class' => 'w-1/2']),
                        //->helperText('Only uppercase letters allowed.')
                        //->disabled()
                        //->dehydrated(false),   // evita que el valor se envíe al servidor

                    Textarea::make('description')
                        ->label('Description')
                        ->required()
                        ->autosize()
                        ->afterStateUpdated(fn ($state, callable $set) => $set('description', ucfirst(strtolower($state))))
                        ->helperText('Please provide a brief description of the operative status.')
                        ->extraAttributes(['class' => 'w-1/2']),
                        //->helperText('Please provide a brief description of the operative status.')
                        //->disabled()
                        //->dehydrated(false),   // evita que el valor se envíe al servidor
                        
                ]),



            ]);
    }




public static function infolist(Schema $schema): Schema
{
    return $schema->components([
        /* ─────────────────────────  PROFILE  ───────────────────────── */
        Section::make('Operative Status Profile')
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
                                ->state(fn ($record) => $record->acronym ? strtoupper($record->acronym) : '—')
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

    ]);
}















    public static function table(Table $table): Table
    {
        return $table
            ->recordUrl(fn (OperativeStatus $record) => static::getUrl('view', ['record' => $record]))
            ->columns([
                //
                TextColumn::make('id')->sortable(),
                TextColumn::make('acronym')->searchable()->sortable(),
                TextColumn::make('description')->searchable()->sortable(),
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
            'index' => ListOperativeStatuses::route('/'),
            'create' => CreateOperativeStatuses::route('/create'),
            'view'   => ViewOperativeStatuses::route('/{record}'),   // 👈 NUEVA
            'edit' => EditOperativeStatuses::route('/{record}/edit'),
        ];
    }
}
