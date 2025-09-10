<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RegionsResource\Pages;
use App\Filament\Resources\RegionsResource\RelationManagers;
use App\Models\Region;
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


class RegionsResource extends Resource
{
    protected static ?string $model = Region::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Resources';
    protected static ?int    $navigationSort  = 2;   // aparecerá primero

    /* ───── NUEVO: burbuja con el total en el menú ───── */
    public static function getNavigationBadge(): ?string
    {
        return Region::count();
    }

    public static function canCreate(): bool
    {
        // Devuelve false para ocultar el botón “New country”
        return false;
    }
    

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
                Section::make('Region Details')
                ->columns(1)    // ← aquí defines dos columnas
                ->schema([

                    TextInput::make('name')
                    ->label('Name')
                    ->required()
                    ->maxLength(255)
                    ->afterStateUpdated(fn ($state, callable $set) => $set('name', ucwords(strtolower($state))))
                    ->helperText('First letter of each word will be capitalised.')
                    ->disabled()
                    ->dehydrated(false),   // evita que el valor se envíe al servidor

                    TextInput::make('region_code')
                    ->label('Region Code')
                    ->required()
                    ->numeric()
                    ->minValue(1) // opcional: evita 0 o negativos
                    ->maxValue(999) // opcional: para limitar a 3 dígitos
                    ->helperText('Only whole numbers allowed.')
                    ->disabled()
                    ->dehydrated(false),   // evita que el valor se envíe al servidor
                    
                ]),    

            ]);
    }




public static function infolist(Infolist $infolist): Infolist
{
    return $infolist->schema([
        /* ─────────────────────────  PROFILE  ───────────────────────── */
        InfoSection::make('Region Profile')->schema([
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

                            // Region Code
                            InfoGrid::make(12)
                                ->extraAttributes([
                                    'style' => 'border-bottom:1px solid rgba(255,255,255,0.12); padding:2px 0;',
                                ])
                                ->schema([
                                    TextEntry::make('code_label')
                                        ->label('')
                                        ->state('Region Code:')
                                        ->weight('bold')
                                        ->alignment('right')
                                        ->columnSpan(3),
                                    TextEntry::make('code_value')
                                        ->label('')
                                        ->state(fn ($record) => $record->region_code !== null ? (string) $record->region_code : '—')
                                        ->columnSpan(9),
                                ]),
                        ]),

                    
                ]),
        ])
        ->maxWidth('4xl')
        ->collapsible(),

        /* ─────────────────────────  AUDIT  ───────────────────────── */
        InfoSection::make('Audit Dates')
            ->schema([
                InfoGrid::make(2)
                    ->extraAttributes(['style' => 'gap: 12px;'])
                    ->schema([
                        InfoGrid::make(12)
                            ->extraAttributes([
                                'style' => 'border-bottom:1px solid rgba(255,255,255,0.12); padding:2px 0;',
                            ])
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
                            ->extraAttributes([
                                'style' => 'border-bottom:1px solid rgba(255,255,255,0.12); padding:2px 0;',
                            ])
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
            ->maxWidth('4xl')
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
                    ->sortable(),
                TextColumn::make('region_code')
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),   // 👈 sustituto de Edit
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
            'index' => Pages\ListRegions::route('/'),
            //'create' => Pages\CreateRegions::route('/create'),
            //'edit' => Pages\EditRegions::route('/{record}/edit'),
        ];
    }
}
