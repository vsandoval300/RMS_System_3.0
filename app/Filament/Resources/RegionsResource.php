<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RegionsResource\Pages;
use App\Filament\Resources\RegionsResource\RelationManagers;
use App\Models\Regions;
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

class RegionsResource extends Resource
{
    protected static ?string $model = Regions::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Resources';


    

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
                    ->extraAttributes(['class' => 'w-1/2']),

                    TextInput::make('region_code')
                    ->label('Region Code')
                    ->required()
                    ->numeric()
                    ->minValue(1) // opcional: evita 0 o negativos
                    ->maxValue(999) // opcional: para limitar a 3 dígitos
                    ->helperText('Only whole numbers allowed.')
                    ->extraAttributes(['class' => 'w-1/2']),

                ]),    

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
                TextColumn::make('id')->sortable(),
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('region_code')->searchable()->sortable(),
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
            'index' => Pages\ListRegions::route('/'),
            'create' => Pages\CreateRegions::route('/create'),
            'edit' => Pages\EditRegions::route('/{record}/edit'),
        ];
    }
}
