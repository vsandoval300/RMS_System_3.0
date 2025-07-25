<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CostSchemeResource\Pages;
use App\Filament\Resources\CostSchemeResource\RelationManagers;
use App\Models\CostScheme;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Icetalker\FilamentTableRepeater\Forms\Components\TableRepeater;
use App\Models\Partner;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class CostSchemeResource extends Resource
{
    protected static ?string $model = CostScheme::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Underwritten';
    protected static ?int    $navigationSort  = 8;   // aparecerá primero


    protected static ?string $navigationLabel = 'Placement Schemes';
    protected static ?string $modelLabel = 'Placement Scheme';

     /* ───── NUEVO: burbuja con el total en el menú ───── */
    public static function getNavigationBadge(): ?string
    {
        return CostScheme::count();
    } 
    

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Structure Details')
                ->schema([
                    Forms\Components\Grid::make()
                        ->schema([
                            // 🔹 Columna izquierda: Share & Structure Agreement
                            Forms\Components\Section::make()
                                ->schema([
                                    Forms\Components\TextInput::make('share')
                                        ->label('Share (%)')
                                        ->numeric()
                                        ->required()
                                        ->suffix('%')
                                        ->formatStateUsing(fn ($state) => $state !== null ? number_format($state * 100, 2, '.', '') : null)
                                        ->dehydrateStateUsing(fn ($state) => $state !== null ? $state / 100 : null),

                                    Forms\Components\Select::make('agreement_type')
                                        ->label('Structure Agreement')
                                        ->required()
                                        ->options([
                                            'Quota Share'     => 'Quota Share',
                                            'Surplus'         => 'Surplus',
                                            'Excess of Loss'  => 'Excess of Loss',
                                            'Stop Loss'       => 'Stop Loss',
                                        ])
                                        ->native(false)
                                        ->searchable(),
                                ])
                                ->columns(1)
                                ->columnSpan(6) // Mitad izquierda
                                ->compact(),

                            // 🔹 Columna derecha: Index & Scheme Id
                            Forms\Components\Section::make()
                                ->schema([
                                    Forms\Components\Grid::make(2)
                                        ->schema([
                                            Forms\Components\TextInput::make('index')
                                                ->label('Index')
                                                ->numeric()
                                                ->required()
                                                ->disabled()
                                                ->dehydrated()
                                                ->columnSpan(1),

                                            Forms\Components\TextInput::make('id')
                                                ->label('Scheme Id')
                                                ->disabled()
                                                ->dehydrated()
                                                ->required()
                                                ->afterStateHydrated(fn ($component, $state) => $component->state($state))
                                                ->columnSpan(1),
                                        ]),
                                ])
                                ->columnSpan(6) // Mitad derecha
                                ->compact(),
                        ])
                        ->columns(12),
                ])
                ->collapsible(),
                // ╔═════════════════════════════════════════════════════════════════════════╗
                // ║ Table Repeater para Nodos de Costo                                      ║
                // ╚═════════════════════════════════════════════════════════════════════════╝
                Forms\Components\Section::make('Cost Nodes')
                    ->description('Define los nodos de costo de este esquema')
                    ->schema([
                        TableRepeater::make('costNodexes')
                            ->label('Cost Nodes')
                            ->relationship('costNodexes')
                            ->schema([
                                // 👇 Campo oculto que sí se guarda
                                Forms\Components\TextInput::make('id')
                                    ->dehydrated(),
                                    //->hidden(), // No se muestra, pero se guarda

                                Forms\Components\TextInput::make('index')
                                    ->label('Index')
                                    ->numeric()
                                    ->required()
                                    ->disabled()
                                    ->dehydrated()
                                    ->columnSpan(1),

                                Forms\Components\Select::make('concept')
                                    ->label('Deduction Type')
                                    ->relationship('deduction', 'concept')
                                    ->preload()
                                    ->searchable()
                                    ->required()
                                    ->columnSpan(2),

                                Forms\Components\Select::make('partner_id')
                                    ->label('Partner')
                                    ->options(Partner::all()->pluck('name', 'id'))
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->columnSpan(1),

                                Forms\Components\TextInput::make('referral_partner')
                                    ->label('Referral Partner')
                                    ->maxLength(255)
                                    ->nullable()
                                    ->columnSpan(2),

                                Forms\Components\TextInput::make('value')
                                    ->label('Value')
                                    ->numeric()
                                    ->required()
                                    ->suffix('%')
                                    ->columnSpan(1),
                            ])
                            ->columns(8)
                            ->defaultItems(1)
                            ->addActionLabel('Agregar nodo de costo')
                            ->reorderable(false)
                            ->deletable(true)
                            ->addable(true)

                            // 👇 Lógica automática para index y id
                            ->afterStateUpdated(function (array $state, callable $set, callable $get) {
                                $schemeId = $get('id'); // Asegúrate que este valor no es null

                                if (! $schemeId || ! is_string($schemeId)) {
                                    return;
                                }

                                $newState = [];
                                $index = 1;

                                foreach ($state as $key => $item) {
                                    if (is_array($item)) {
                                        $item['index'] = $index;
                                        $item['id'] = $schemeId . '-' . str_pad((string) $index, 2, '0', STR_PAD_LEFT);
                                        $newState[$key] = $item;
                                        $index++;
                                    }
                                }

                                $set('costNodexes', $newState);
                            }),

                    ])
                    ->collapsible(),






        ]);
    }





    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('index')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('id')
                    ->searchable(),
                Tables\Columns\TextColumn::make('share')
                    ->label('Share')
                    ->formatStateUsing(fn ($state) => number_format($state * 100, 2) . '%'),
                Tables\Columns\TextColumn::make('agreement_type'),
                Tables\Columns\TextColumn::make('created_at')->since(),
            ])
            ->defaultSort('index', 'asc')
            ->filters([])
            
             ->actions([
                Tables\Actions\ActionGroup::make([
                    /* Tables\Actions\ViewAction::make()
                    ->label('View')
                    ->url(fn (CostScheme $record) =>
                        self::getUrl('view', ['record' => $record])
                    )
                    ->icon('heroicon-m-eye'),  // opcional */
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ])

            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            // Puedes añadir un RelationManager para costNodes más adelante si lo deseas
           // RelationManagers\CostNodexesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCostSchemes::route('/'),
            'create' => Pages\CreateCostScheme::route('/create'),
            //'view' => Pages\ViewCostScheme::route('/{record}'),
            'edit' => Pages\EditCostScheme::route('/{record}/edit'),
        ];
    }
}