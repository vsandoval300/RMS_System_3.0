<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HoldingResource\Pages;
use App\Filament\Resources\HoldingResource\RelationManagers;
use App\Models\Holding;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\TextColumn;

class HoldingResource extends Resource
{
    protected static ?string $model = Holding::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Compliance';
    protected static ?int    $navigationSort  = 1;   // aparecerá primero

    /* ───── NUEVO: burbuja con el total en el menú ───── */
    public static function getNavigationBadge(): ?string
    {
        // Puedes usar self::$model::count() o Reinsurer::count()
        return Holding::count();
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Holding Profile')
                    ->compact()
                    ->schema([
                    Forms\Components\Grid::make(2)->schema([            
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(400),
                        Forms\Components\TextInput::make('short_name')
                            ->required()
                            ->maxLength(60),
                        Forms\Components\Select::make('country_id')
                            ->relationship('country', 'name')
                            ->required(),
                        Forms\Components\Select::make('client_id')
                            ->relationship('client', 'name')
                            ->required(),
                        ]),
                    ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('Index')
                    ->label('Index')
                    ->state(fn ($record, $rowLoop) => $rowLoop->iteration)
                    ->sortable(false) // 👈 no tiene sentido ordenar este índice
                    ->searchable(false), // 👈 tampoco buscarlo

                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('short_name')
                    ->searchable(),
                TextColumn::make('country.name')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('client.name')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
            'index' => Pages\ListHoldings::route('/'),
            'create' => Pages\CreateHolding::route('/create'),
            'edit' => Pages\EditHolding::route('/{record}/edit'),
        ];
    }
}
