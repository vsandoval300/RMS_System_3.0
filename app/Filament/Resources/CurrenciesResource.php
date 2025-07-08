<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CurrenciesResource\Pages;
use App\Filament\Resources\CurrenciesResource\RelationManagers;
use App\Models\Currency;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\FormsComponent;
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

class CurrenciesResource extends Resource
{
    protected static ?string $model = Currency::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Resources';
    protected static ?int    $navigationSort  = 1;   // aparecerá primero

    /* ───── NUEVO: burbuja con el total en el menú ───── */
    public static function getNavigationBadge(): ?string
    {
        // Puedes usar self::$model::count() o Reinsurer::count()
        return Currency::count();
    }

    public static function canCreate(): bool
    {
        // Devuelve false para ocultar el botón “New country”
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
                Section::make('Currency Details')
                ->columns(1)    // ← aquí defines dos columnas
                ->schema([  

                    TextInput::make('name')
                    ->label('Name')
                    ->required()
                    ->maxLength(255)
                    ->afterStateUpdated(fn ($state, callable $set) => $set('name', ucwords(strtolower($state))))
                    //->helperText('First letter of each word will be capitalised.')
                    ->disabled()
                    ->dehydrated(false),   // evita que el valor se envíe al servidor

                    TextInput::make('acronym')
                    ->label('Acronym')
                    ->required()
                    ->maxLength(3)
                    ->rule('regex:/^[A-Z]+$/')
                    ->afterStateUpdated(fn ($state, callable $set) => $set('acronym', strtoupper($state)))
                    //->helperText('Only uppercase letters allowed.')
                    ->disabled()
                    ->dehydrated(false),   // evita que el valor se envíe al servidor
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
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('acronym')
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),   // 👈 sustituto de Edit
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
            'index' => Pages\ListCurrencies::route('/'),
            //'create' => Pages\CreateCurrencies::route('/create'),
            //'edit' => Pages\EditCurrencies::route('/{record}/edit'),
        ];
    }
}
