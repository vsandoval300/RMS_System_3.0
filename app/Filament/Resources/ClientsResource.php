<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ClientsResource\Pages;
use App\Filament\Resources\ClientsResource\RelationManagers;
use App\Models\Client;
use App\Models\Country;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Resources\RelationManagers\Concerns\BelongsToManyRelationManager;


class ClientsResource extends Resource
{
    protected static ?string $model = Client::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Clients';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
                Section::make('Client Details')
                ->columns(2)    // ← aquí defines dos columnas
                ->schema([
                    

                    TextInput::make('name')
                        ->label('Name')
                        ->required()
                        ->unique()
                        ->maxLength(255)
                        ->afterStateUpdated(fn ($state, callable $set) => $set('name', ucwords(strtolower($state))))
                        ->helperText('First letter of each word will be capitalised.'),
                        
                    
                    TextInput::make('short_name')
                        ->label('Short Name')
                        ->required()
                        ->unique()
                        ->live(onBlur: false)
                        ->maxLength(255)
                        ->afterStateUpdated(fn ($state, callable $set) => $set('short_name', ucwords(strtolower($state))))
                        ->helperText('First letter of each word will be capitalised.'),
                       
                    
                    Textarea::make('description')
                        ->label('Description')
                        ->required()
                        ->columnSpan('full')
                        ->autosize()
                        ->afterStateUpdated(fn ($state, callable $set) => $set('description', ucfirst(strtolower($state))))
                        ->helperText('Please provide a brief description of the sector. Only the first letter will be capitalised.'),
                        

                    TextInput::make('webpage')
                        ->label('Web Page')
                        ->required()
                        ->maxLength(255)
                        ->helperText('First letter of each word will be capitalised.'),
                        

                    Select::make('country_id')
                        ->label('Country')
                        ->options(function () {
                            return Country::orderBy('name')
                                ->get()
                                ->mapWithKeys(fn ($country) => [
                                    $country->id => "{$country->alpha_3} - {$country->name}"
                                ]);
                        })
                        ->searchable()
                        ->preload()
                        ->required()
                        ->placeholder('Select a country')
                        ->helperText('Choose the reinsurer\'s country.'),
                        

                ]),

                Section::make('Images')->schema([

                    FileUpload::make('logo_path')
                        ->label('Logo')
                        ->disk('s3')
                        ->directory('reinsurers/logos')
                        ->image()
                        ->visibility('public')
                        ->default(fn ($record) => $record?->logo)
                        ->imagePreviewHeight('100')
                        ->previewable()
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
                        'style' => 'width: 200px; white-space: normal;', // ✅ Deja que el texto se envuelva
                    ]),
                TextColumn::make('short_name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('description')
                    ->label('Description')
                    ->wrap()
                    ->extraAttributes([
                        'style' => 'width: 520px; white-space: normal;', // ancho fijo de 300px
                    ]),
                TextColumn::make('webpage')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                    
                TextColumn::make('country.name')
                    ->label('Country')
                    ->sortable()
                    ->searchable()
                    ->toggleable()
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([

                    Tables\Actions\ViewAction::make()
                        ->label('View')
                        ->url(fn (Client $record) =>
                            ClientsResource::getUrl('view', ['record' => $record])
                ),
                       
                    //Tables\Actions\ViewAction::make(),
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
            
           RelationManagers\IndustriesRelationManager::class,
        
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListClients::route('/'),
            'create' => Pages\CreateClients::route('/create'),
            'view'   => Pages\ViewClients::route('/{record}'),  // 👈 nuevo
            'edit'   => Pages\EditClients::route('/{record}/edit'),
        ];
    }
}
