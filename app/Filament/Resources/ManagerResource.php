<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ManagerResource\Pages;
use App\Filament\Resources\ManagerResource\RelationManagers;
use App\Models\Manager;
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
use Filament\Tables\Columns\TextColumn;

// 👇 IMPORTS para INFOLIST
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section as InfoSection;
use Filament\Infolists\Components\Grid as InfoGrid;
use Filament\Infolists\Components\TextEntry;

class ManagerResource extends Resource
{
    protected static ?string $model = Manager::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
     protected static ?string $navigationGroup = 'Resources';
     protected static ?int    $navigationSort  = 10;   // aparecerá primero

   /* ───── NUEVO: burbuja con el total en el menú ───── */
    public static function getNavigationBadge(): ?string
    {
        // Puedes usar self::$model::count() o Reinsurer::count()
        return Manager::count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
                Section::make('Manager Details')
                ->columns(1)    // ← aquí defines dos columnas
                ->schema([

                    TextInput::make('name')
                    ->label('Name')
                    ->required()
                    ->unique()
                    ->live(onBlur: false)
                    ->maxLength(255)
                    ->afterStateUpdated(fn ($state, callable $set) => $set('name', ucwords(strtolower($state))))
                    ->helperText('First letter of each word will be capitalised.')
                    ->extraAttributes(['class' => 'w-1/2']),
                //
                ]),
            ]);
    }



public static function infolist(Infolist $infolist): Infolist
{
    return $infolist->schema([
        /* ─────────────────────────  PROFILE  ───────────────────────── */
        InfoSection::make('Manager Profile')->schema([
            InfoGrid::make(1)
                ->extraAttributes(['style' => 'row-gap: 0;'])
                ->schema([

                    // Name
                    InfoGrid::make(12)
                        ->extraAttributes([
                            'style' => 'border-bottom:1px solid rgba(255,255,255,0.12); padding:2px 0;',
                        ])
                        ->schema([
                            TextEntry::make('name_label')
                                ->label('')
                                ->state('Name:')
                                ->weight('bold')
                                ->alignment('right')
                                ->columnSpan(3),
                            TextEntry::make('name_value')
                                ->label('')
                                ->state(fn ($record) => $record->name ?: '—')
                                ->columnSpan(9),
                        ]),
                ]),
        ])
        ->maxWidth('5xl')
        ->collapsible(),

        /* ─────────────────────────  AUDIT  ───────────────────────── */
        InfoSection::make('Audit Dates')->schema([
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
            'index' => Pages\ListManagers::route('/'),
            'create' => Pages\CreateManager::route('/create'),
            'edit' => Pages\EditManager::route('/{record}/edit'),
        ];
    }
}
