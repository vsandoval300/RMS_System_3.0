<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PositionResource\Pages;
use App\Filament\Resources\PositionResource\RelationManagers;
use App\Models\Position;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

// 👇 IMPORTS para INFOLIST
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section as InfoSection;
use Filament\Infolists\Components\Grid as InfoGrid;
use Filament\Infolists\Components\TextEntry;

class PositionResource extends Resource
{
    protected static ?string $model = Position::class;

    protected static ?string $navigationLabel = 'Positions';
    protected static ?string $navigationIcon = 'heroicon-o-briefcase';
    protected static ?string $navigationGroup = 'Security'; // o Security
    protected static ?int $navigationSort = 30;

    public static function getNavigationBadge(): ?string
    {
        return Position::count();
    }

    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            Section::make('Position Details')
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



public static function infolist(Infolist $infolist): Infolist
{
    return $infolist->schema([
        /* ─────────────────────────  PROFILE  ───────────────────────── */
        InfoSection::make('Position Profile')->schema([
            InfoGrid::make(1)
                ->extraAttributes(['style' => 'row-gap: 0;'])
                ->schema([

                    // Name
                    InfoGrid::make(12)
                        ->extraAttributes(['style' => 'border-bottom:1px solid rgba(255,255,255,0.12); padding:2px 0;'])
                        ->schema([
                            TextEntry::make('id')
                                ->label('')
                                ->state('Id:')
                                ->weight('bold')
                                ->alignment('right')
                                ->columnSpan(3),
                            TextEntry::make('id_value')
                                ->label('')
                                ->state(fn ($record) => $record->id ?: '—')
                                ->columnSpan(9),
                        ]),

                    // Acronym
                    InfoGrid::make(12)
                        ->extraAttributes(['style' => 'border-bottom:1px solid rgba(255,255,255,0.12); padding:2px 0;'])
                        ->schema([
                            TextEntry::make('position')
                                ->label('')
                                ->state('Position:')
                                ->weight('bold')
                                ->alignment('right')
                                ->columnSpan(3),
                            TextEntry::make('pos_value')
                                ->label('')
                                ->state(fn ($record) => $record->position ? strtoupper($record->position) : '—')
                                ->columnSpan(9),
                        ]),

                    // Description
                    InfoGrid::make(12)
                        ->extraAttributes(['style' => 'border-bottom:1px solid rgba(255,255,255,0.12); padding:2px 0;'])
                        ->schema([
                            TextEntry::make('description')
                                ->label('')
                                ->state('Description:')
                                ->weight('bold')
                                ->alignment('right')
                                ->columnSpan(3),
                            TextEntry::make('desc_value')
                                ->label('')
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
            'index' => Pages\ListPositions::route('/'),
            'create' => Pages\CreatePosition::route('/create'),
            'view'   => Pages\ViewPosition::route('/{record}'),   // 👈 NUEVA
            'edit' => Pages\EditPosition::route('/{record}/edit'),
        ];
    }
}
