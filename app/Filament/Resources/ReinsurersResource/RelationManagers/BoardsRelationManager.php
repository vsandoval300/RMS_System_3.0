<?php

namespace App\Filament\Resources\ReinsurersResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Support\Enums\VerticalAlignment;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ViewColumn;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Repeater;
use App\Models\Director;
use App\Models\Board;
use App\Models\ReinsurerBoard;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;

class BoardsRelationManager extends RelationManager
{
    protected static string $relationship = 'boards';
    protected static ?string $icon = 'heroicon-o-user-group';

    public function form(Form $form): Form
    {
        
         return $form->schema([
            TextInput::make('index')
            ->label('Index')
            ->default(fn () => 'BoD-' . now()->format('Ymd') . '-' . random_int(100000, 999999))
            ->disabled()          // no editable
            ->dehydrated(true)    // que sí se envíe al backend
            ->required()
            ->maxLength(255)
            ->columnSpanFull(),

            // 👇 Nuevo campo para la fecha de nombramiento
            DatePicker::make('appt_date')
            ->label('Appointment Date')
            ->native(false)
            ->closeOnDateSelection()
            ->required()
            ->columnSpanFull(),

            Select::make('directors')
            ->label('Directors')
            ->relationship('directors') // relación belongsToMany en Board
            ->getOptionLabelFromRecordUsing(
                fn (Director $d) => trim($d->name . ' ' . $d->surname)
            )
            ->multiple()
            ->searchable()
            ->preload()
            ->columnSpanFull(),
        ]); 
    }





    public function table(Table $table): Table
    {
        return $table
            // 👇 SIN type-hint en el parámetro
            ->modifyQueryUsing(fn ($query) => $query->with('directors'))
            ->recordTitleAttribute('index')
            ->columns([
                /* ───── Index ───── */
                

                TextColumn::make('row')
                    ->label('Index')
                    ->verticalAlignment(VerticalAlignment::Start)
                    ->state(fn ($record, $rowLoop) => $rowLoop->iteration)
                    ->sortable(false) // 👈 no tiene sentido ordenar este índice
                    ->searchable(false), // 👈 tampoco buscarlo

                TextColumn::make('index')
                    ->label('Board Id')
                    ->verticalAlignment(VerticalAlignment::Start)
                    ->sortable() 
                    ->searchable(),   

                /* ───── Appointment ───── */
                TextColumn::make('pivot.appt_date')
                    ->label('Appointment')
                    ->verticalAlignment(VerticalAlignment::Start) 
                    ->date(),

                /* ───── Directors ───── */
                TextColumn::make('directors')
                    ->label('Directors')
                    ->verticalAlignment(VerticalAlignment::Start)  
                    ->html()
                    ->state(function ($record) {
                        return $record->directors        // colección de modelos Director
                            ->map(fn ($d) => trim($d->name . ' ' . $d->surname))  // «Nombre Apellido»
                            ->implode('<br>');           // separa cada uno con salto de línea
                    }),

                /* ───── Occupations ───── */
                TextColumn::make('occupations')
                    ->label('Occupation')
                    ->verticalAlignment(VerticalAlignment::Start) 
                    ->html()
                    ->state(fn ($record) =>
                        $record->directors
                            ->map(fn ($d) => e($d->occupation ?? '—'))
                            ->implode('<br>')
                    ),

            ])
            ->defaultSort('index', 'asc')
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Add Board')
                    ->using(function (array $data, RelationManager $livewire) {
                        // usa el que vino del form; si no, genera uno nuevo
                        $index = $data['index'] ?? ('BoD-' . now()->format('Ymd') . '-' . random_int(100000, 999999));

                        $board = Board::create([
                            'index' => $index,
                        ]);

                        $livewire->getOwnerRecord()
                            ->boards()
                            ->attach($board->getKey(), [
                                'appt_date' => $data['appt_date'],
                            ]);

                        return $board;
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->using(function (Board $record, RelationManager $livewire) {
                        // Reinsurer al que pertenece este RelationManager
                        $reinsurer = $livewire->getOwnerRecord();

                        // Buscar el pivot específico
                        $pivot = ReinsurerBoard::where('reinsurer_id', $reinsurer->id)
                            ->where('board_id', $record->id)
                            ->first();

                        if ($pivot) {
                            // 👇 SOLO esto: dispara SoftDeletes + evento "deleted" + HasAuditLogs
                            $pivot->delete();
                        }

                        // Filament espera que regreses el modelo "borrado"
                        return $record;
                    }),
            ]);
    }
}
