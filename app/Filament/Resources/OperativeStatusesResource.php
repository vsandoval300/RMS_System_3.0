<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OperativeStatusesResource\Pages;
use App\Filament\Resources\OperativeStatusesResource\RelationManagers;
use App\Models\OperativeStatus;
use App\Models\OperativeStatuses;
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

class OperativeStatusesResource extends Resource
{
    protected static ?string $model = OperativeStatus::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Resources';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
                Section::make('Operative Status Details')
                ->columns(1)    // ← aquí defines dos columnas
                ->schema([

                    TextInput::make('acronym')
                        ->label('Acronym')
                        ->required()
                        ->maxLength(2)
                        ->rule('regex:/^[A-Z]+$/')
                        ->afterStateUpdated(fn ($state, callable $set) => $set('acronym', strtoupper($state)))
                        ->helperText('Only uppercase letters allowed.')
                        ->extraAttributes(['class' => 'w-1/2']),
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
                TextColumn::make('id')->sortable(),
                TextColumn::make('acronym')->searchable()->sortable(),
                TextColumn::make('description')->searchable()->sortable(),
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
            'index' => Pages\ListOperativeStatuses::route('/'),
            'create' => Pages\CreateOperativeStatuses::route('/create'),
            'edit' => Pages\EditOperativeStatuses::route('/{record}/edit'),
        ];
    }
}
