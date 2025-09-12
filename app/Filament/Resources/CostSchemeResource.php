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
use Filament\Forms\Components\Repeater;
use App\Models\Partner;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\TextInput\Mask;
use Filament\Support\RawJs;


// 👇 IMPORTS para INFOLIST
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section as InfoSection;
use Filament\Infolists\Components\Grid as InfoGrid;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\RepeatableEntry;

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
            Section::make('Structure Details')
                ->schema([
                    Forms\Components\Grid::make()
                        ->schema([
                            // 🔹 Columna izquierda: Share & Structure Agreement
                            Forms\Components\Section::make()
                                ->schema([
                                    Forms\Components\Grid::make(2)
                                        ->schema([
                                            Select::make('agreement_type')
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

                                            TextInput::make('share')
                                                ->label('Share (%)')
                                                ->numeric()
                                                ->required()
                                                ->suffix('%')
                                                ->minValue(0)  
                                                ->maxValue(100)
                                                ->formatStateUsing(fn ($state) => $state !== null ? number_format($state * 100, 2, '.', '') : null)
                                                ->dehydrateStateUsing(fn ($state) => $state !== null ? $state / 100 : null),
                                        ]),
                                ])
                                ->columns(1)
                                ->columnSpan(6) // Mitad izquierda
                                ->compact(),
                            
                            

                            // 🔹 Columna derecha: Index & Scheme Id
                            Section::make()
                                ->schema([
                                    Forms\Components\Grid::make(2)
                                        ->schema([
                                            TextInput::make('index')
                                                ->label('Index')
                                                ->numeric()
                                                ->required()
                                                ->disabled()
                                                ->dehydrated()
                                                ->columnSpan(1),

                                            TextInput::make('id')
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
                Section::make('Cost Nodes')
                    ->description('Define the cost nodes of this scheme')
                        ->schema([
                            Repeater::make('costNodexes')
                                ->label('')
                                ->relationship('costNodexes')
                                ->default([])
                                ->schema([
                                    // 👇 Campo oculto que sí se guarda
                                    Hidden::make('id')
                                        ->dehydrated(),
                                        //->hidden(), // No se muestra, pero se guarda

                                    Hidden::make('index')
                                        ->dehydrated(), // 👈 este es el que se guarda en BD

                                    Placeholder::make('index_display')
                                        ->label('Index')
                                        ->content(fn ($get) => $get('index'))
                                        ->columnSpan(1),

                                    Select::make('concept')
                                        ->label('Deduction Type')
                                        ->relationship('deduction', 'concept')
                                        ->placeholder('Select deduction')
                                        ->preload()
                                        ->searchable()
                                        ->required()
                                        ->reactive() // 👈 necesario para disparar dependencias
                                        ->afterStateUpdated(function ($state, callable $set) {
                                            if ($state != 3) {   // 👈 si NO es referral
                                                $set('referral_partner', null); // 👈 limpia el valor
                                            }
                                        })
                                        ->columnSpan(2),

                                    Select::make('partner_source_id')
                                        ->label('Source')
                                        ->options(Partner::all()->pluck('short_name', 'id'))
                                        ->placeholder('Select partner')
                                        ->searchable()
                                        ->preload()
                                        ->required()
                                        ->columnSpan(3),

                                    Select::make('partner_destination_id')
                                        ->label('Destination')
                                        ->options(Partner::all()->pluck('short_name', 'id'))
                                        ->placeholder('Select partner')
                                        ->searchable()
                                        ->preload()
                                        ->required()
                                        ->columnSpan(3),

                                    TextInput::make('value')
                                        ->label('Value')
                                        ->suffix('%')
                                        ->type('text')
                                        ->inputMode('decimal') 
                                        ->live(onBlur: true)
                                        ->required()
                                        ->minValue(0)
                                        ->maxValue(100)
                                        ->formatStateUsing(fn ($state) => $state !== null ? number_format($state * 100, 5, '.', '') : null)
                                        ->dehydrateStateUsing(fn ($state) => $state !== null ? $state / 100 : null)
                                        ->extraInputAttributes([
                                            'class' => 'text-right tabular-nums', // ← alinea a la derecha y usa dígitos monoespaciados
                                        ])
                                        ->columnSpan(2),

                            ])
                            ->columns(11)
                            ->addActionLabel('Agregar nodo de costo')
                            ->reorderable(false)
                            ->deletable(true)
                            ->addable(true)

                            // ⬇️ Al agregar/quitar filas, reindexas y luego recalculas el total
                            ->afterStateUpdated(function (array $state, callable $set, callable $get) {
                                $schemeId = $get('id');

                                if (! $schemeId || ! is_string($schemeId)) {
                                    return;
                                }

                                // Reindexación/ID (tu lógica)
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

                                // 🟢 Recalcular total tras add/remove
                                $total = collect($newState)
                                    ->pluck('value')
                                    ->filter()
                                    ->map(fn ($v) => (float) $v)
                                    ->sum();

                                $set('total_values', number_format($total, 5, '.', ''));
                            }),

                        
                                /* Placeholder::make('total_values')
                                    ->label('')
                                    ->content(function ($get) {
                                        $values = collect($get('costNodexes') ?? [])
                                            ->pluck('value')
                                            ->filter()
                                            ->map(fn ($v) => floatval($v)); // 👈 multiplicamos porque en BD está dividido entre 100
                                        $total = $values->sum();
                                        return 'Total deductions: ' . $total . '%';
                                    })
                                    ->columnSpanFull()
                                    ->extraAttributes(['class' => 'text-right font-bold']), */

                                

                                

                                Group::make()
                                    ->schema([
                                        TextInput::make('total_values')
    ->label('Total deductions')
    ->suffix('%')
    ->readOnly()
    ->dehydrated(false)
    ->extraInputAttributes(['class' => 'text-right tabular-nums'])
    ->afterStateHydrated(function ($set, $get) {
        $rows   = $get('costNodexes') ?? [];
        $values = collect($rows)
            ->pluck('value')
            ->filter(fn ($v) => $v !== null && $v !== '');

        if ($values->isEmpty()) {
            $set('total_values', null);
            return;
        }

        // 🔢 decimales máximos observados en los nodos
        $maxDp = $values->map(function ($v) {
            $s = str_replace(',', '', (string) $v);
            $p = strpos($s, '.');
            return $p === false ? 0 : strlen(substr($s, $p + 1));
        })->max();

        // ➕ suma con precisión y formatea con $maxDp
        if (function_exists('bcadd')) {
            $sum = '0';
            foreach ($values as $v) {
                $sum = bcadd($sum, (string) str_replace(',', '.', (string) $v), 20);
            }
            $display = bcadd($sum, '0', $maxDp);
        } else {
            $sum     = $values->reduce(fn ($c, $v) => $c + (float) str_replace(',', '.', (string) $v), 0.0);
            $display = number_format($sum, $maxDp, '.', '');
        }

        $set('total_values', $display);
                                            }),
                                    ])
                                    // ocupa el ancho de la fila pero alinea el input a la derecha
                                    ->extraAttributes(['class' => 'w-full flex justify-end'])
                                    ->columnSpanFull(),



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
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('share')
                    ->label('Share')
                    ->formatStateUsing(fn ($state) => number_format($state * 100, 2) . '%'),
                Tables\Columns\TextColumn::make('agreement_type'),
                Tables\Columns\TextColumn::make('created_at')->since(),
                Tables\Columns\TextColumn::make('updated_at')->since(),
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
                    Tables\Actions\ViewAction::make()
                     ->modalWidth('7xl'), // 👈 aumenta el ancho del modal
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