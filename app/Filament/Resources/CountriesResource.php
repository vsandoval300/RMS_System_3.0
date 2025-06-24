<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CountriesResource\Pages;
use App\Filament\Resources\CountriesResource\RelationManagers;
use App\Models\Countries;
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

class CountriesResource extends Resource
{
    protected static ?string $model = Countries::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Resources';


   

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
                Grid::make(1)->schema([

                    TextInput::make('name')
                    ->label('Name')
                    ->required()
                    ->maxLength(255)
                    ->afterStateUpdated(fn ($state, callable $set) => $set('name', ucwords(strtolower($state))))
                    ->helperText('First letter of each word will be capitalised.')
                    ->extraAttributes(['class' => 'w-1/2']),

                    TextInput::make('alpha_2')
                    ->label('Alpha 2')
                    ->required()
                    ->maxLength(2)
                    ->rule('regex:/^[A-Z]+$/')
                    ->afterStateUpdated(fn ($state, callable $set) => $set('alpha_2', strtoupper($state)))
                    ->helperText('Only uppercase letters allowed.')
                    ->extraAttributes(['class' => 'w-1/2']),

                    TextInput::make('alpha_3')
                    ->label('Alpha 3')
                    ->required()
                    ->maxLength(3)
                    ->rule('regex:/^[A-Z]+$/')
                    ->afterStateUpdated(fn ($state, callable $set) => $set('alpha_3', strtoupper($state)))
                    ->helperText('Only uppercase letters allowed.')
                    ->extraAttributes(['class' => 'w-1/2']),

                    TextInput::make('country_code')
                    ->label('Country Code')
                    ->required()
                    ->numeric()
                    ->minValue(1) // opcional: evita 0 o negativos
                    ->maxValue(999) // opcional: para limitar a 3 dígitos
                    ->helperText('Only whole numbers allowed.')
                    ->extraAttributes(['class' => 'w-1/2']),

                    TextInput::make('iso_code')
                    ->label('Iso Code')
                    ->required()
                    ->maxLength(30)
                    ->rule('regex:/^[A-Z0-9\-:\s]+$/')
                    ->afterStateUpdated(fn ($state, callable $set) => $set('iso_code', strtoupper($state)))
                    ->helperText('Only uppercase letters, numbers, dash (-), colon (:), and spaces allowed.')
                    ->extraAttributes(['class' => 'w-1/2']),

                    TextInput::make('am_best_code')
                    ->label('AM Best Code')
                    ->required()
                    ->maxLength(10)
                    ->rule('regex:/^[A-Z0-9\-\s]+$/')
                    ->afterStateUpdated(fn ($state, callable $set) => $set('am_best_code', strtoupper($state)))
                    ->helperText('Only uppercase letters, numbers, dash (-), and spaces allowed.')
                    ->extraAttributes(['class' => 'w-1/2']),

                    TextInput::make('latitude')
                    ->label('Latitude')
                    ->required()
                    ->type('number') // ✅ convierte el input en <input type="number">
                    ->step('any')    // ✅ permite cualquier cantidad de decimales
                    ->minValue(-90)  // límite geográfico para latitud
                    ->maxValue(90)
                    ->helperText('Enter a decimal value between -90 and 90.')
                    ->extraAttributes(['class' => 'w-1/2']),

                    TextInput::make('longitude')
                    ->label('longitude')
                    ->required()
                    ->type('number') // ✅ convierte el input en <input type="number">
                    ->step('any')    // ✅ permite cualquier cantidad de decimales
                    ->minValue(-90)  // límite geográfico para latitud
                    ->maxValue(90)
                    ->helperText('Enter a decimal value between -90 and 90.')
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
                TextColumn::make('alpha_2')->searchable()->sortable(),
                TextColumn::make('alpha_3')->searchable()->sortable(),
                TextColumn::make('country_code')->searchable()->sortable(),
                TextColumn::make('iso_code')->searchable()->sortable(),
                TextColumn::make('am_best_code')->searchable()->sortable(),
                TextColumn::make('latitude')->searchable()->sortable(),
                TextColumn::make('longitude')->searchable()->sortable(),
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
            'index' => Pages\ListCountries::route('/'),
            'create' => Pages\CreateCountries::route('/create'),
            'edit' => Pages\EditCountries::route('/{record}/edit'),
        ];
    }
}
