<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BanksResource\Pages;
use App\Filament\Resources\BanksResource\RelationManagers;
use App\Models\Banks;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
//use App\Filament\Clusters\Resources;

class BanksResource extends Resource
{
    protected static ?string $model = Banks::class;
    // protected static ?string $cluster = Resources::class; // ✅ Vinculación correcta
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    //protected static ?string $cluster = \App\Filament\Clusters\Resources::class;
    protected static ?string $navigationGroup = 'Resources';

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

                    Forms\Components\Textarea::make('address')
                        ->label('Address')
                        ->required()
                        ->columnSpan('full')
                        ->afterStateUpdated(fn ($state, callable $set) => $set('address', ucfirst(strtolower($state))))
                        ->helperText('Please provide address.')
                        ->extraAttributes(['class' => 'w-1/2']),

                    Forms\Components\TextInput::make('aba_number')
                        ->label('ABA number')
                        ->maxLength(255)
                        ->afterStateUpdated(fn ($state, callable $set) => $set('aba_number', ucwords(strtolower($state))))
                        ->helperText('Please provide ABA number.')
                        ->extraAttributes(['class' => 'w-1/2']),   
                        
                    Forms\Components\TextInput::make('swift_code')
                        ->label('SWIFT code')
                        ->required()
                        ->maxLength(255)
                        ->afterStateUpdated(fn ($state, callable $set) => $set('swift_code', ucwords(strtolower($state))))
                        ->helperText('Please provide SWIFT code.')
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
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('address')
                    ->label('Address')
                    ->sortable()
                    ->searchable()
                    ->wrap() // ✅ Permite que se haga multilínea
                    ->extraAttributes([
                        'class' => 'max-w-xl whitespace-normal', // ✅ Deja que el texto se envuelva
                    ]),
                Tables\Columns\TextColumn::make('aba_number')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('swift_code')->searchable()->sortable(),



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
            'index' => Pages\ListBanks::route('/'),
            'create' => Pages\CreateBanks::route('/create'),
            'edit' => Pages\EditBanks::route('/{record}/edit'),
        ];
    }
}
