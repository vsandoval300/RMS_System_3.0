<?php

namespace App\Filament\Resources\Positions;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Actions\ActionGroup;
use Filament\Actions\ViewAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Resources\Positions\Pages\ListPositions;
use App\Filament\Resources\Positions\Pages\CreatePosition;
use App\Filament\Resources\Positions\Pages\ViewPosition;
use App\Filament\Resources\Positions\Pages\EditPosition;
use App\Filament\Resources\PositionResource\Pages;
use App\Filament\Resources\PositionResource\RelationManagers;
use App\Models\Position;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Infolists\Components\TextEntry;

class PositionResource extends Resource
{
    protected static ?string $model = Position::class;

    protected static ?string $navigationLabel = 'Positions';
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-briefcase';
    protected static string | \UnitEnum | null $navigationGroup = 'Security'; // o Security
    protected static ?int $navigationSort = 30;

    public static function getNavigationBadge(): ?string
    {
        return Position::count();
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
        ->components([
            Section::make('Position Details')
                ->columnSpan('full')
                ->schema([
                    TextInput::make('position')
                        ->label('Position')
                        ->required()
                        ->maxLength(255)
                        ->unique(ignoreRecord: true),

                    Textarea::make('description')
                        ->label('Description')
                        ->rows(4)
                        ->columnSpanFull(),
                ])
                ->columns(1),
        ]);
    }



public static function infolist(Schema $schema): Schema
{
    return $schema->components([
        /* ─────────────────────────  PROFILE  ───────────────────────── */
        Section::make('Position Profile')
        ->columnSpan('full')
        ->schema([
            Grid::make(1)
                ->extraAttributes(['style' => 'row-gap: 0;'])
                ->schema([

                    // Name
                    Grid::make(12)
                        ->extraAttributes(['style' => 'border-bottom:1px solid rgba(255,255,255,0.12); padding:2px 0;'])
                        ->schema([
                            TextEntry::make('id')
                                ->hiddenLabel()
                                ->state('Id:')
                                ->weight('bold')
                                ->alignment('right')
                                ->columnSpan(3),
                            TextEntry::make('id_value')
                                ->hiddenLabel()
                                ->state(fn ($record) => $record->id ?: '—')
                                ->columnSpan(9),
                        ]),

                    // Acronym
                    Grid::make(12)
                        ->extraAttributes(['style' => 'border-bottom:1px solid rgba(255,255,255,0.12); padding:2px 0;'])
                        ->schema([
                            TextEntry::make('position')
                                ->hiddenLabel()
                                ->state('Position:')
                                ->weight('bold')
                                ->alignment('right')
                                ->columnSpan(3),
                            TextEntry::make('pos_value')
                                ->hiddenLabel()
                                ->state(fn ($record) => $record->position ? strtoupper($record->position) : '—')
                                ->columnSpan(9),
                        ]),

                    // Description
                    Grid::make(12)
                        ->extraAttributes(['style' => 'border-bottom:1px solid rgba(255,255,255,0.12); padding:2px 0;'])
                        ->schema([
                            TextEntry::make('description')
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
            ->columns([
            TextColumn::make('id')
                ->label('Id')
                ->sortable(),
                //->toggleable(isToggledHiddenByDefault: true),

            TextColumn::make('position')
                ->label('Position')
                ->searchable()
                ->sortable(),
                //->weight('bold'),

            TextColumn::make('description')
                ->label('Description')
                ->wrap()
                ->searchable()
                ->extraAttributes([
                    'class' => 'max-w-xl whitespace-pre-line break-words',
                ]),

            TextColumn::make('created_at')
                ->label('Created')
                ->date()
                ->toggleable(isToggledHiddenByDefault: true),
        ])
        ->defaultSort('id', 'asc')
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
            'index' => ListPositions::route('/'),
            'create' => CreatePosition::route('/create'),
            'view'   => ViewPosition::route('/{record}'),   // 👈 NUEVA
            'edit' => EditPosition::route('/{record}/edit'),
        ];
    }
}
