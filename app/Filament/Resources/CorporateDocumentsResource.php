<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CorporateDocumentsResource\Pages;
use App\Filament\Resources\CorporateDocumentsResource\RelationManagers;
use App\Models\DocumentType;
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

class CorporateDocumentsResource extends Resource
{
    protected static ?string $model = DocumentType::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Corporate Documents';
    protected static ?string $navigationGroup = 'Resources';
    protected static ?int    $navigationSort  = 9;   // aparecerá primero

    /* ───── NUEVO: burbuja con el total en el menú ───── */
    public static function getNavigationBadge(): ?string
    {
        // Puedes usar self::$model::count() o Reinsurer::count()
        return DocumentType::count();
    }
    

    public static function form(Form $form): Form
{
    return $form
        ->schema([
            Forms\Components\Section::make('Corporate Document Details')
                ->schema([
                    TextInput::make('name')
                        ->label('Name')
                        ->required()
                        ->maxLength(255)
                        ->unique()
                        ->afterStateUpdated(fn ($state, callable $set) => $set('name', ucwords(strtolower($state))))
                        ->helperText('First letter of each word will be capitalised.')
                        ->columnSpan('full'),

                    TextInput::make('acronym')
                        ->label('Acronym')
                        ->required()
                        ->maxLength(2)
                        ->rule('regex:/^[A-Z]+$/')
                        ->afterStateUpdated(fn ($state, callable $set) => $set('acronym', strtoupper($state)))
                        ->unique()
                        ->helperText('Provide two characters — only uppercase letters allowed (e.g. “US”).')
                        ->columnSpan('full'),

                    Textarea::make('description')
                        ->label('Description')
                        ->required()
                        ->autosize()
                        ->afterStateUpdated(fn ($state, callable $set) => $set('description', ucfirst(strtolower($state))))
                        ->helperText('Please provide a brief description of the document.')
                        ->columnSpan('full'),
                ])
                ->columns(1)          // 👈 todos los campos ocupan fila completa
                ->columnSpanFull(),   // 👈 fuerza a que la sección se estire al 100% del modal
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
                
                TextColumn::make('acronym')
                    ->searchable()
                    ->sortable(),
                
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
            'index' => Pages\ListCorporateDocuments::route('/'),
            'create' => Pages\CreateCorporateDocuments::route('/create'),
            'edit' => Pages\EditCorporateDocuments::route('/{record}/edit'),
        ];
    }
}
