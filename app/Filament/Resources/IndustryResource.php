<?php

namespace App\Filament\Resources;

use App\Filament\Resources\IndustryResource\Pages;
use App\Filament\Resources\SectorsResource\RelationManagers;
use App\Models\Industry;
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
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;

class IndustryResource extends Resource
{
    protected static ?string $model = Industry::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
     protected static ?string $navigationLabel = 'Sectors';
    protected static ?string $navigationGroup = 'Resources';
    protected static ?int    $navigationSort  = 5;   // aparecerá primero

    /* ───── NUEVO: burbuja con el total en el menú ───── */
    public static function getNavigationBadge(): ?string
    {
        // Puedes usar self::$model::count() o Reinsurer::count()
        return Industry::count();
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
                Section::make('Industry Details')
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

                    Textarea::make('description')
                    ->label('Description')
                    ->required()
                    ->columnSpan('full')
                    ->autosize()
                    ->afterStateUpdated(fn ($state, callable $set) => $set('description', ucfirst(strtolower($state))))
                    //->helperText('Please provide a brief description of the sector. Only the first letter will be capitalised.')
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
                    ->label('Name')
                    ->sortable()
                    ->searchable()
                    ->extraAttributes([
                        'class' => 'min-w-[12rem] whitespace-nowrap',
                    ]),
                
                TextColumn::make('description')
                    ->label('Description')
                    ->sortable()
                    ->searchable()
                    ->wrap() // ✅ Permite que se haga multilínea
                    ->extraAttributes([
                        'class' => 'max-w-xl whitespace-normal', // ✅ Deja que el texto se envuelva
                    ]),


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
            'index' => Pages\ListIndustries::route('/'),
            //'create' => Pages\CreateIndustries::route('/create'),
            //'edit' => Pages\EditIndustries::route('/{record}/edit'),
        ];
    }
}
