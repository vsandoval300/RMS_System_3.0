<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BusinessDocTypesResource\Pages;
use App\Filament\Resources\BusinessDocTypesResource\RelationManagers;
use App\Models\BusinessDocType;
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
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;

// 👇 IMPORTS para INFOLIST
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section as InfoSection;
use Filament\Infolists\Components\Grid as InfoGrid;
use Filament\Infolists\Components\TextEntry;

class BusinessDocTypesResource extends Resource
{
    protected static ?string $model = BusinessDocType::class;
    protected static ?string $navigationIcon = 'heroicon-o-minus';
    protected static ?string $navigationLabel = 'Business Document Types';
    protected static ?string $navigationGroup = 'Underwritten';
    protected static ?int    $navigationSort  = 7;   // aparecerá primero

    public static function getNavigationBadge(): ?string
    {
        return BusinessDocType::count(); // o self::$model::count()
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
                Section::make('Business Document Type')
                ->columns(1)    // ← aquí defines dos columnas
                ->schema([

                    TextInput::make('name')
                        ->label('Name')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->maxLength(255)
                        ->afterStateUpdated(fn ($state, callable $set) => $set('name', ucwords(strtolower($state))))
                        ->helperText('First letter of each word will be capitalised.')
                        ->extraAttributes(['class' => 'w-1/2']),

                    Textarea::make('description')
                        ->label('Description')
                        ->required()
                        ->columnSpan('full')
                        ->autosize()
                        ->afterStateUpdated(fn ($state, callable $set) => $set('description', ucfirst(strtolower($state))))
                        ->helperText('Please provide a brief description of the sector. Only the first letter will be capitalised.')
                        ->extraAttributes(['class' => 'w-1/2']),

                ]),
            ]);
    }





    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            /* ─────────────────────────  PROFILE  ───────────────────────── */
            InfoSection::make('Business Document Type')->schema([
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
                                    ->extraAttributes(['style' => 'border-bottom:1px solid rgba(255,255,255,0.12); padding:2px 2px;'])
                                    ->schema([
                                        TextEntry::make('name_label')->label('')->state('Name:')
                                            ->weight('bold')->alignment('right')->columnSpan(3),
                                        TextEntry::make('name_value')->label('')
                                            ->state(fn ($record) => $record->name ?: '—')
                                            ->columnSpan(9),
                                    ]),

                                // Description
                                InfoGrid::make(12)
                                    ->extraAttributes(['style' => 'border-bottom:1px solid rgba(255,255,255,0.12); padding:2px 2px;'])
                                    ->schema([
                                        TextEntry::make('desc_label')->label('')->state('Description:')
                                            ->weight('bold')->alignment('right')->columnSpan(3),
                                        TextEntry::make('desc_value')->label('')
                                            ->state(fn ($record) => $record->description ?: '—')
                                            ->extraAttributes(['style' => 'line-height:1.35;'])
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
                            ->extraAttributes(['style' => 'border-bottom:1px solid rgba(255,255,255,0.12); padding:2px 2px;'])
                            ->schema([
                                TextEntry::make('created_label')->label('')->state('Created At:')
                                    ->weight('bold')->alignment('right')->columnSpan(3),
                                TextEntry::make('created_value')->label('')
                                    ->state(fn ($record) => $record->created_at?->format('Y-m-d H:i') ?: '—')
                                    ->columnSpan(9),
                            ]),
                        // Updated at
                        InfoGrid::make(12)
                            ->extraAttributes(['style' => 'border-bottom:1px solid rgba(255,255,255,0.12); padding:2px 2px;'])
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
                    ->searchable()
                    ->sortable()
                    ->extraAttributes([
                        'style' => 'width: 300px; white-space: normal;', // ✅ Deja que el texto se envuelva
                    ]),
                TextColumn::make('description')
                    ->label('Description')
                    ->wrap()
                    ->extraAttributes([
                        'style' => 'width: 600px; white-space: normal;', // ancho fijo de 300px
                    ]),
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
            'index' => Pages\ListBusinessDocTypes::route('/'),
            'create' => Pages\CreateBusinessDocTypes::route('/create'),
            'edit' => Pages\EditBusinessDocTypes::route('/{record}/edit'),
        ];
    }
}
