<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LineOfBusinessResource\Pages;
use App\Filament\Resources\LineOfBusinessResource\RelationManagers;
use App\Models\LineOfBusiness;
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

class LineOfBusinessResource extends Resource
{
    protected static ?string $model = LineOfBusiness::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Lines of Businesses';
    protected static ?string $navigationGroup = 'Underwritten';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
                Section::make('Line of Business')
                ->columns(1)    // ← aquí defines dos columnas
                ->schema([

                    TextInput::make('name')
                    ->label('Name')
                    ->required()
                    ->unique()
                    ->maxLength(255)
                    ->afterStateUpdated(fn ($state, callable $set) => $set('name', ucwords(strtolower($state))))
                    ->helperText('First letter of each word will be capitalised.')
                    ->extraAttributes(['class' => 'w-1/2']),

                    Textarea::make('description')
                    ->label('Description')
                    ->required()
                    ->columnSpan('full')
                    ->autosize()
                    ->afterStateUpdated(fn ($state, callable $set) => $set('description', ucfirst(strtolower($state))))
                    ->helperText('Please provide a brief description of the sector. Only the first letter will be capitalised.')
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
                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->extraAttributes([
                        'style' => 'width: 180px; white-space: normal;', // ✅ Deja que el texto se envuelva
                    ]),
                TextColumn::make('description')
                    ->label('Description')
                    ->wrap()
                    ->extraAttributes([
                        'style' => 'width: 600px; white-space: normal;', // ancho fijo de 300px
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
            'index' => Pages\ListLineOfBusinesses::route('/'),
            'create' => Pages\CreateLineOfBusiness::route('/create'),
            'edit' => Pages\EditLineOfBusiness::route('/{record}/edit'),
        ];
    }
}
