<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CompaniesResource\Pages;
use App\Filament\Resources\CompaniesResource\RelationManagers;
use App\Models\Companies;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CompaniesResource extends Resource
{
    protected static ?string $model = Companies::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Underwritten';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
                Forms\Components\Grid::make(1)->schema([

                    Forms\Components\TextInput::make('name')
                    ->label('Name')
                    ->required()
                    ->maxLength(255)
                    ->afterStateUpdated(fn ($state, callable $set) => $set('name', ucwords(strtolower($state))))
                    ->helperText('First letter of each word will be capitalised.')
                    ->extraAttributes(['class' => 'w-1/2']),

                    Forms\Components\TextInput::make('acronym')
                    ->label('Acronym')
                    ->required()
                    ->maxLength(2)
                    ->rule('regex:/^[A-Z]+$/')
                    ->afterStateUpdated(fn ($state, callable $set) => $set('acronym', strtoupper($state)))
                    ->helperText('Only uppercase letters allowed.')
                    ->extraAttributes(['class' => 'w-1/2']),

                    Forms\Components\Textarea::make('activity')
                    ->label('Description')
                    ->required()
                    ->columnSpan('full')
                    ->autosize()
                    ->afterStateUpdated(fn ($state, callable $set) => $set('description', ucfirst(strtolower($state))))
                    ->helperText('Please provide a brief description of the sector. Only the first letter will be capitalised.')
                    ->extraAttributes(['class' => 'w-1/2']),

                    Forms\Components\Select::make('country_id')
                        ->label('Country')
                        ->relationship('country','name')
                        ->searchable()
                        ->preload()
                        ->required()
                        ->extraAttributes(['class' => 'w-1/2']),




                    Forms\Components\Select::make('industry_id')
                        ->label('Sector')
                        ->relationship('sector','name')
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
                Tables\Columns\TextColumn::make('id')->sortable(),
                Tables\Columns\TextColumn::make('name')->searchable()->sortable()
                ->extraAttributes([
                        'style' => 'width: 320px; white-space: normal;', // ✅ Deja que el texto se envuelva
                    ]),
                Tables\Columns\TextColumn::make('acronym')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('activity')
                    ->label('Activity')
                    ->wrap()
                    ->extraAttributes([
                        'style' => 'width: 550px; white-space: normal;', // ancho fijo de 300px
                    ]),

                Tables\Columns\TextColumn::make('sector.name')
                    ->label('Sector')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('country.name')
                    ->label('Country')
                    ->sortable()
                    ->searchable()


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
            'index' => Pages\ListCompanies::route('/'),
            'create' => Pages\CreateCompanies::route('/create'),
            'edit' => Pages\EditCompanies::route('/{record}/edit'),
        ];
    }
}
