<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReinsurersResource\Pages;
use App\Filament\Resources\ReinsurersResource\RelationManagers;
use App\Models\Reinsurers;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ReinsurersResource extends Resource
{
    protected static ?string $model = Reinsurers::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Reinsurers';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
                Tables\Columns\TextColumn::make('id')->sortable()
                ->extraAttributes([
                        'style' => 'width: 30px; white-space: normal;', // ✅ Deja que el texto se envuelva
                    ]),
                Tables\Columns\TextColumn::make('name')->searchable()->sortable()
                ->extraAttributes([
                        'style' => 'width: 320px; white-space: normal;', // ✅ Deja que el texto se envuelva
                    ]),

                Tables\Columns\TextColumn::make('short_name')->searchable()->sortable(),    
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListReinsurers::route('/'),
            'create' => Pages\CreateReinsurers::route('/create'),
            'edit' => Pages\EditReinsurers::route('/{record}/edit'),
        ];
    }
}
