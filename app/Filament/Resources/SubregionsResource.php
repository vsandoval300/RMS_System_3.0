<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SubregionsResource\Pages;
use App\Filament\Resources\SubregionsResource\RelationManagers;
use App\Models\Subregion;
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

class SubregionsResource extends Resource
{
    protected static ?string $model = Subregion::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Resources';


    

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
                Section::make('Subregion Details')
                ->columns(1)    // ← aquí defines dos columnas
                ->schema([
                    
                    TextInput::make('name')
                    ->label('Name')
                    ->required()
                    ->maxLength(255)
                    ->afterStateUpdated(fn ($state, callable $set) => $set('name', ucwords(strtolower($state))))
                    ->helperText('First letter of each word will be capitalised.')
                    ->extraAttributes(['class' => 'w-1/2']),

                    TextInput::make('subregion_code')
                    ->label('Subregion Code')
                    ->required()
                    ->numeric()
                    ->minValue(1) // opcional: evita 0 o negativos
                    ->maxValue(999) // opcional: para limitar a 3 dígitos
                    ->helperText('Only whole numbers allowed.')
                    ->extraAttributes(['class' => 'w-1/2']),

                    Select::make('region_id')
                    ->label('Region')
                    ->relationship('region', 'name')
                    ->searchable()
                    ->preload()
                    ->required()
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
                TextColumn::make('subregion_code')->searchable()->sortable(),
                TextColumn::make('region.name')
                ->label('Region')
                ->sortable(),


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
            'index' => Pages\ListSubregions::route('/'),
            'create' => Pages\CreateSubregions::route('/create'),
            'edit' => Pages\EditSubregions::route('/{record}/edit'),
        ];
    }
}
