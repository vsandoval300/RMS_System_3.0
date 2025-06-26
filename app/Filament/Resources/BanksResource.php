<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BanksResource\Pages;
use App\Filament\Resources\BanksResource\RelationManagers;
use App\Models\Bank;
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

class BanksResource extends Resource
{
    protected static ?string $model = Bank::class;
    // protected static ?string $cluster = Resources::class; // ✅ Vinculación correcta
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    //protected static ?string $cluster = \App\Filament\Clusters\Resources::class;
    protected static ?string $navigationGroup = 'Banks';
    protected static ?int    $navigationSort  = 9;   // aparecerá primero

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make() //Grupo 1
                ->schema([
                    Forms\Components\Section::make('Bank Details')
                    ->schema([
                    
                    TextInput::make('name')
                        ->label('Name')
                        ->required()
                        ->unique()
                        ->maxLength(255)
                        ->afterStateUpdated(fn ($state, callable $set) => $set('name', ucwords(strtolower($state))))
                        ->helperText('First letter of each word will be capitalised.'),
                        
                    Textarea::make('address')
                        ->label('Address')
                        ->required()
                        ->columnSpan('full')
                        ->afterStateUpdated(fn ($state, callable $set) => $set('address', ucfirst(strtolower($state))))
                        ->helperText('Please provide address.'),
                        
                    TextInput::make('aba_number')
                        ->label('ABA number')
                        ->unique()
                        ->maxLength(255)
                        ->afterStateUpdated(fn ($state, callable $set) => $set('aba_number', ucwords(strtolower($state))))
                        ->helperText('Please provide ABA number.'),
                           
                    TextInput::make('swift_code')
                        ->label('SWIFT code')
                        ->required()
                        ->unique()
                        ->maxLength(255)
                        ->afterStateUpdated(fn ($state, callable $set) => $set('swift_code', ucwords(strtolower($state))))
                        ->helperText('Please provide SWIFT code.'),
                               
                    ]),
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
                TextColumn::make('address')
                    ->label('Address')
                    ->sortable()
                    ->searchable()
                    ->wrap() // ✅ Permite que se haga multilínea
                    ->extraAttributes([
                        'class' => 'max-w-xl whitespace-normal', // ✅ Deja que el texto se envuelva
                    ]),
                TextColumn::make('aba_number')
                ->searchable()
                ->sortable(),
                TextColumn::make('swift_code')
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
            'index' => Pages\ListBanks::route('/'),
            'create' => Pages\CreateBanks::route('/create'),
            'edit' => Pages\EditBanks::route('/{record}/edit'),
        ];
    }
}
