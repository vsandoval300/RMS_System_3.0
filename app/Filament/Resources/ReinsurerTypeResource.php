<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReinsurerTypeResource\Pages;
use App\Filament\Resources\ReinsurerTypeResource\RelationManagers;
use App\Models\ReinsurerType;
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

class ReinsurerTypeResource extends Resource
{
    protected static ?string $model = ReinsurerType::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Resources';
    protected static ?int    $navigationSort  = 8;   // aparecerá primero

    /* ───── NUEVO: burbuja con el total en el menú ───── */
    public static function getNavigationBadge(): ?string
    {
        return ReinsurerType::count();
    }


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
                Section::make('Reinsurer Type Details')
                ->columns(1)    // ← aquí defines dos columnas
                ->schema([

                    TextInput::make('type_acronym')
                        ->label('Acronym')
                        ->required()
                        ->unique()
                        ->maxLength(2)
                        ->rule('regex:/^[A-Z]+$/')
                        ->afterStateUpdated(fn ($state, callable $set) => $set('acronym', strtoupper($state)))
                        ->helperText('Only uppercase letters allowed.')
                        ->extraAttributes(['class' => 'w-1/2'])
                        ->live(onBlur: false),
                        //->extraAttributes(['class' => 'uppercase w-32']),

                    TextInput::make('description')
                        ->label('Description')
                        ->required()
                        ->maxLength(255)
                        ->afterStateUpdated(fn ($state, callable $set) => $set('name', ucwords(strtolower($state))))
                        ->helperText('Please provide a brief description of the operative status.')
                        ->extraAttributes(['class' => 'w-1/2']),
                        
                ]),


            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
                TextColumn::make('id')
                    ->sortable(),
                TextColumn::make('type_acronym')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('description')
                    ->searchable()
                    ->sortable(),



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
            'index' => Pages\ListReinsurerTypes::route('/'),
            'create' => Pages\CreateReinsurerType::route('/create'),
            'edit' => Pages\EditReinsurerType::route('/{record}/edit'),
        ];
    }
}
