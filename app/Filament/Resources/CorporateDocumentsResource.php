<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CorporateDocumentsResource\Pages;
use App\Filament\Resources\CorporateDocumentsResource\RelationManagers;
use App\Models\CorporateDoc;
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
    protected static ?string $model = CorporateDoc::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Corporate Documents';
    protected static ?string $navigationGroup = 'Resources';

    

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make() //Grupo 1
                ->schema([
                    Forms\Components\Section::make('Corporate Document Details') //Sección 1
                    ->schema([

                    TextInput::make('name')
                    ->label('Name')
                    ->required()
                    ->maxLength(255)
                    ->afterStateUpdated(fn ($state, callable $set) => $set('name', ucwords(strtolower($state))))
                    ->helperText('First letter of each word will be capitalised.'),

                   TextInput::make('acronym')
                    ->label('Acronym')
                    ->required()
                    ->maxLength(2)
                    ->rule('regex:/^[A-Z]+$/')
                    ->afterStateUpdated(fn ($state, callable $set) => $set('acronym', strtoupper($state)))
                    ->helperText('Provide two characters — only uppercase letters allowed (e.g. “US”).'),

                    Textarea::make('description')
                    ->label('Description')
                    ->required()
                    ->columnSpan('full')
                    ->autosize()
                    ->afterStateUpdated(fn ($state, callable $set) => $set('description', ucfirst(strtolower($state))))
                    ->helperText('Please provide a brief description of the document.'),

                ]),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
                TextColumn::make('id')->sortable(),
                TextColumn::make('name')
                    ->label('Name')
                    ->sortable()
                    ->searchable()
                    ->extraAttributes([
                        'class' => 'min-w-[12rem] whitespace-nowrap',
                    ]),
                
                TextColumn::make('acronym')->searchable()->sortable(),
                
                
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
            'index' => Pages\ListCorporateDocuments::route('/'),
            'create' => Pages\CreateCorporateDocuments::route('/create'),
            'edit' => Pages\EditCorporateDocuments::route('/{record}/edit'),
        ];
    }
}
