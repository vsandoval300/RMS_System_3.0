<?php

namespace App\Filament\Resources\ReinsurersResource\RelationManagers;

use App\Models\Holding;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\TextColumn;
use Filament\Support\RawJs;
use Illuminate\Validation\ValidationException;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Get;

class ReinsurerHoldingsRelationManager extends RelationManager
{
    protected static string $relationship = 'reinsurerHoldings';
    protected static ?string $title = 'Holdings';

    public function form(Form $form): Form
    {
        return $form->schema([
            // útil para excluirse en edición al calcular "restante"
            //Hidden::make('id')->dehydrated(),

            Select::make('holding_id')
                ->label('Holding')
                ->relationship('holding', 'name')
                ->searchable()
                ->preload()
                ->required(),

            TextInput::make('percentage')
                ->label('Participation Percentage')
                ->suffix('%')
                ->required()
                ->live()
                ->minValue(0)
                ->maxValue(100)
                ->step(0.01)
                ->mask(RawJs::make('$money($input, ".", ",", 2)')) // visual 70.00
                ->reactive()
                // Mostrar % disponible en vivo
                ->helperText(function (Get $get, RelationManager $livewire) {
                    $owner = $livewire->getOwnerRecord();
                    $editingId = $get('id');

                    $sumOthers = $owner->reinsurerHoldings()
                        ->when($editingId, fn ($q) => $q->whereKeyNot($editingId))
                        ->sum('percentage'); // decimal

                    $enteredPercent = (float) str_replace(',', '', (string) $get('percentage')); // "60.00"
                    $entered = $enteredPercent ? $enteredPercent / 100 : 0;

                    $remain = max(0, 1 - $sumOthers - $entered);
                    return 'Available: ' . number_format($remain * 100, 2) . '%';
                })
                // ⬇️ REGLA que muestra el error debajo del campo
                ->rule(fn (Get $get, RelationManager $livewire) =>
                    function (string $attribute, $value, \Closure $fail) use ($get, $livewire) {
                        $owner = $livewire->getOwnerRecord();
                        $editingId = $get('id');

                        $sumOthers = $owner->reinsurerHoldings()
                            ->when($editingId, fn ($q) => $q->whereKeyNot($editingId))
                            ->sum('percentage'); // decimal

                        // $value llega como porcentaje ("60" o "60.00")
                        $entered = ((float) str_replace(',', '', (string) $value)) / 100;

                        if (round($sumOthers + $entered, 6) > 1) {
                            $remaining = max(0, 1 - $sumOthers);
                            $fail('The sum of Participation Percentage exceeds 100%. Remaining value to assign: '
                                . number_format($remaining * 100, 2) . '%');
                        }
                    }
                )
                // conversiones: decimal <-> porcentaje
                ->formatStateUsing(fn ($state) => $state !== null ? round($state * 100, 2) : null) // decimal → %
                ->dehydrateStateUsing(fn ($state) => floatval(str_replace(',', '', $state)) / 100)  // % → decimal
                ->columnSpan(1),

        ]);
    }




    public function table(Table $table): Table
    {
        return $table
            // 🏎 Eager-load para evitar N+1
            ->modifyQueryUsing(fn (Builder $query) =>
                $query->with(['holding.country', 'holding.client'])
            )

            // Muestra el nombre del holding como título de registro
            ->recordTitleAttribute('holding.name')

            ->columns([
                TextColumn::make('index')
                    ->label('Index')
                    ->state(fn ($record, $rowLoop) => $rowLoop->iteration)
                    ->sortable(false) // 👈 no tiene sentido ordenar este índice
                    ->searchable(false), // 👈 tampoco buscarlo
                // Holding
                TextColumn::make('holding.name')
                    ->label('Holding')
                    ->searchable()
                    ->sortable(),

                // País
                TextColumn::make('holding.country.name')
                    ->label('Country')
                    ->sortable(),

                // Cliente
                TextColumn::make('holding.client.name')
                    ->label('Client')
                    ->sortable(),

                // % formateado
                TextColumn::make('percentage')
                    ->label('Participation Share')
                    ->alignCenter()                      // alinea a la derecha
                    ->formatStateUsing(fn ($state) => number_format($state * 100, 2) . '%')
                    ->sortable(),
            ])

            ->headerActions([ 
                Tables\Actions\CreateAction::make()
                ->label('Add Holding'), 
            ]) 
                
            ->actions([ Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])

            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
