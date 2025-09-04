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
    protected static ?int    $navigationSort  = 1;   // aparecerá primero

    /* ───── NUEVO: burbuja con el total en el menú ───── */
    public static function getNavigationBadge(): ?string
    {
        // Puedes usar self::$model::count() o Reinsurer::count()
        return Bank::count();
    }

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
                            ->placeholder('Please provide name')
                            ->required()
                            ->unique()
                            ->maxLength(255)
                            ->afterStateUpdated(fn ($state, callable $set) => $set('name', ucwords(strtolower($state)))),
                            /* ->helperText(fn (string $context) => in_array($context, ['create', 'edit']) 
                                ? 'First letter of each word will be capitalised.' 
                                : null), */
                            
                        Textarea::make('address')
                            ->label('Address')
                            ->placeholder('Please provide bank address')
                            ->required()
                            ->columnSpan('full')
                            ->afterStateUpdated(fn ($state, callable $set) => $set('address', ucfirst(strtolower($state)))),
                            /* ->helperText(fn (string $context) => in_array($context, ['create', 'edit']) 
                                ? 'Please provide address.' 
                                : null), */
                            
                        TextInput::make('aba_number')
                            ->label('ABA number')
                            ->placeholder('Please provide ABA number.')
                            ->rule('digits:9') 
                            ->unique()
                            ->maxLength(255)
                            ->afterStateUpdated(fn ($state, callable $set) => $set('aba_number', ucwords(strtolower($state))))
                            ->helperText(fn (string $context) => in_array($context, ['create', 'edit']) 
                                ? '9 digits, e.g. 123456789' 
                                : null),
                            
                        TextInput::make('swift_code')
                            ->label('SWIFT Code')
                            ->placeholder('Please provide SWIFT code.')
                            ->required()
                            ->rule('regex:/^[A-Z0-9]{8}([A-Z0-9]{3})?$/') // 8 o 11 caracteres alfanuméricos
                            ->unique(ignoreRecord: true)
                            ->maxLength(11) // 👈 opcional: restringir a máximo 11 chars
                            ->afterStateUpdated(fn ($state, callable $set) => $set('swift_code', strtoupper($state)))
                            ->extraAttributes(['style' => 'text-transform:uppercase'])
                            ->helperText(fn (string $context) => in_array($context, ['create', 'edit']) 
                                ? '8 or 11 characters, e.g. DEUTDEFF500' 
                                : null), 
                               
                    ])
                    ->columns(2),
                ])
                ->columnSpanFull(),
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
                    //->modalWidth('md'),
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
