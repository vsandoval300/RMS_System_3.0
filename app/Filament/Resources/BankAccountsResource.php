<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BankAccountsResource\Pages;
use App\Filament\Resources\BankAccountsResource\RelationManagers;
use App\Models\BankAccount;
use Filament\Actions\EditAction;
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

class BankAccountsResource extends Resource
{
    protected static ?string $model = BankAccount::class;
    //protected static ?string $cluster = Resources::class; // ✅ Vinculación correcta
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    //protected static ?string $cluster = \App\Filament\Clusters\Resources::class;
    protected static ?string $navigationGroup = 'Resources';
   
   
    
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make() //Grupo 1
                ->schema([
                    Forms\Components\Section::make('Wire Instructions')
                    ->schema([
                            
                            Select::make('status_account')
                                ->label('Status Account') // Cambié el label para que tenga más sentido
                                ->required()
                                ->options([
                                    'active' => 'Active',
                                    'inactive' => 'Inactive',
                                    ])
                                ->native(false) // Para que se vea como dropdown estilizado (opcional)
                                ->helperText('Please select the account status.'),
                                
                            Select::make('currency_id')
                                ->label('Currency')
                                ->relationship('currency','name')
                                ->searchable()
                                ->preload()
                                ->required(),
                               
                            Select::make('intermediary_bank')
                                ->label('Intermediary Bank')
                                ->relationship('bank','name')
                                ->searchable()
                                ->preload()
                                ->required(),
                               
                            Select::make('bank_id')
                                ->label('Bank / For Credit to')
                                ->relationship('bank','name')
                                ->searchable()
                                ->preload()
                                ->required(),
                                
                    ])->columns(1),
                    
                    Forms\Components\Section::make('Beneficiary Details')
                    ->schema([
                            TextInput::make('beneficiary_acct_name')
                                ->label('Beneficiay Name')
                                ->required()
                                ->maxLength(255)
                                ->afterStateUpdated(fn ($state, callable $set) => $set('beneficiary_acct_name', ucwords(strtolower($state))))
                                ->helperText('First letter of each word will be capitalised.'),
                                
                            
                            Textarea::make('beneficiary_address')
                                ->label('Beneficiary Address')
                                ->required()
                                ->columnSpan('full')
                                ->afterStateUpdated(fn ($state, callable $set) => $set('beneficiary_address', ucfirst(strtolower($state))))
                                ->helperText('Please provide address.'),
                                       

                            TextInput::make('beneficiary_swift')
                                ->label('Beneficiary SWIFT code')
                                ->required()
                                ->maxLength(255)
                                ->afterStateUpdated(fn ($state, callable $set) => $set('beneficiary_swift', ucwords(strtolower($state))))
                                ->helperText('Please provide SWIFT code.'),
                                 

                            TextInput::make('beneficiary_acct_no')
                                ->label('Beneficiary Account Number')
                                ->required()
                                ->maxLength(255)
                                ->afterStateUpdated(fn ($state, callable $set) => $set('beneficiary_acct_no', ucwords(strtolower($state))))
                                ->helperText('Please provide account number.'),
                                
                    ])->columns(1),

                    Forms\Components\Section::make('For Further Account Details')
                    ->schema([
                            TextInput::make('ffc_acct_name')
                                ->label('For Further Account Name')
                                ->required()
                                ->maxLength(255)
                                ->afterStateUpdated(fn ($state, callable $set) => $set('ffc_acct_name', ucwords(strtolower($state))))
                                ->helperText('First letter of each word will be capitalised.'),

                            TextInput::make('ffc_acct_no')
                                ->label('For Further Account Number')
                                ->required()
                                ->maxLength(255)
                                ->afterStateUpdated(fn ($state, callable $set) => $set('ffc_acct_no', ucwords(strtolower($state))))
                                ->helperText('Please provide account number.'), 

                            Textarea::make('ffc_acct_address')
                                ->label('For Further Account Address')
                                ->required()
                                ->columnSpan('full')
                                ->afterStateUpdated(fn ($state, callable $set) => $set('ffc_acct_address', ucfirst(strtolower($state))))
                                ->helperText('Please provide address.'),
                                
                    ])->columns(1)
                
                
                
                
                ]),
                Forms\Components\Group::make() //Grupo 2
                ->schema([
                    Forms\Components\Section::make()
                    ->schema([

                    ])->columns(2)
                ])
                   
            ]);
    }           
                
             /*   Section::make('Wire Instructions')
                

                Section::make('For Further Account Details')->schema([

                    TextInput::make('ffc_acct_name')
                        ->label('For Further Account Name')
                        ->required()
                        ->maxLength(255)
                        ->afterStateUpdated(fn ($state, callable $set) => $set('ffc_acct_name', ucwords(strtolower($state))))
                        ->helperText('First letter of each word will be capitalised.')
                        ->extraAttributes(['class' => 'w-1/2']),

                    TextInput::make('ffc_acct_no')
                        ->label('For Further Account Number')
                        ->required()
                        ->maxLength(255)
                        ->afterStateUpdated(fn ($state, callable $set) => $set('ffc_acct_no', ucwords(strtolower($state))))
                        ->helperText('Please provide account number.')
                        ->extraAttributes(['class' => 'w-1/2']), 

                    Textarea::make('ffc_acct_address')
                        ->label('For Further Account Address')
                        ->required()
                        ->columnSpan('full')
                        ->afterStateUpdated(fn ($state, callable $set) => $set('ffc_acct_address', ucfirst(strtolower($state))))
                        ->helperText('Please provide address.')
                        ->extraAttributes(['class' => 'w-1/2']),     

                        ]),  
                  
                ]);
    }*/

    public static function table(Table $table): Table
    {
        return $table
            ->recordUrl(null)          // ← sigue sin abrir la página de edición
            ->selectable()             // ← habilita la selección con clic en la fila
            ->columns([
                //
                TextColumn::make('id')->sortable(),

                TextColumn::make('status_account')
                    ->label('Status')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('currency.name')
                    ->label('Currency')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('bank_inter.name')
                    ->label('Intermediary Bank')
                    ->sortable()
                    ->searchable(),
                
                TextColumn::make('bank.name')
                    ->label('Banks')
                    ->sortable(),

                TextColumn::make('beneficiary_acct_name')
                    ->label('Beneficiary Name')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('beneficiary_address')
                    ->label('Beneficiary Address')
                    ->sortable()
                    ->searchable()
                    ->wrap() // ✅ Permite que se haga multilínea
                    ->extraAttributes([
                        'class' => 'max-w-xl whitespace-normal', // ✅ Deja que el texto se envuelva
                    ]),

                TextColumn::make('beneficiary_swift')
                    ->label('Beneficiary Swift')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('beneficiary_acct_no')
                    ->label('Beneficiary Account')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('ffc_acct_name')
                    ->label('For Further Account Name')
                    ->sortable()
                    ->searchable()
                    ->wrap() // ✅ Permite que se haga multilínea
                    ->extraAttributes([
                        'class' => 'max-w-xl whitespace-normal', // ✅ Deja que el texto se envuelva
                    ]),

                TextColumn::make('ffc_acct_no')
                    ->label('For Further Account Number')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('ffc_acct_address')
                    ->label('For Further Account Address')
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

                Tables\Actions\Action::make('generate_pdf')
                    ->label('PDF')
                    ->icon('heroicon-o-arrow-down-tray') // ícono de descarga
                    ->tooltip('Download PDF of this record')
                    ->color('gray') // o usa 'secondary', 'info', etc.
                    ->action(function ($record) {
                        // Aquí luego implementarás la lógica de generación de PDF
                        // Por ahora puede ser un log, redirección o notificación
                        \Filament\Notifications\Notification::make()
                            ->title('PDF generation triggered for: ' . $record->id)
                            ->success()
                            ->send();
                    }),
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
            'index' => Pages\ListBankAccounts::route('/'),
            'create' => Pages\CreateBankAccounts::route('/create'),
            'edit' => Pages\EditBankAccounts::route('/{record}/edit'),
        ];
    }
}
