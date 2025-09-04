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
    protected static ?string $navigationGroup = 'Banks';
    protected static ?int    $navigationSort  = 2;   // aparecerá primero

    /* ───── NUEVO: burbuja con el total en el menú ───── */
    public static function getNavigationBadge(): ?string
    {
        // Puedes usar self::$model::count() o Reinsurer::count()
        return BankAccount::count();
    }
   

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                //Forms\Components\Group::make() //Grupo 1
                //->schema([
                    Forms\Components\Section::make('Wire Instructions')
                        ->schema([
                            
                            Select::make('status_account')
                                ->label('Status Account') // Cambié el label para que tenga más sentido
                                ->inlineLabel()
                                ->required()
                                ->options([
                                    'Active' => 'Active',
                                    'Inactive' => 'Inactive',
                                    ])
                                ->native(false) // Para que se vea como dropdown estilizado (opcional)
                                ->placeholder('Select account status'),
                                /* ->helperText(fn (string $context) => in_array($context, ['create', 'edit']) 
                                ? 'Please select the account status.' 
                                : null), */
                                
                            Select::make('currency_id')
                                ->label('Currency')
                                ->inlineLabel()
                                ->placeholder('Select currency')
                                ->relationship('currency', 'name') // 👈 relación base
                                ->getOptionLabelFromRecordUsing(
                                    fn ($record) => $record->acronym . ' - ' . $record->name
                                )
                                ->searchable()
                                ->preload()
                                ->required(),
                               
                            Select::make('intermediary_bank')
                                ->label('Intermediary Bank')
                                ->inlineLabel()
                                ->placeholder('Select intermediary bank')
                                ->relationship('bank','name')
                                ->searchable()
                                ->preload()
                                ->required(),
                               
                            Select::make('bank_id')
                                ->label('Bank / For Credit to')
                                ->inlineLabel()
                                ->placeholder('Select bank')
                                ->relationship('bank','name')
                                ->searchable()
                                ->preload()
                                ->required(),
                                
                        ])
                        ->columns(1)
                        ->collapsible(),

                    
                    Forms\Components\Section::make('Beneficiary Details')
                        ->schema([
                            TextInput::make('beneficiary_acct_name')
                                ->label('Beneficiay Name')
                                ->inlineLabel()
                                ->placeholder("Please provide the beneficiary's full name")
                                ->required()
                                ->maxLength(255)
                                ->afterStateUpdated(fn ($state, callable $set) => $set('beneficiary_acct_name', ucwords(strtolower($state)))),
                                //->helperText('First letter of each word will be capitalised.'),
                                
                            Textarea::make('beneficiary_address')
                                ->label('Beneficiary Address')
                                ->inlineLabel()
                                ->placeholder("Please provide beneficiary adress")
                                ->required()
                                ->columnSpan('full')
                                ->afterStateUpdated(fn ($state, callable $set) => $set('beneficiary_address', ucfirst(strtolower($state)))),

                            TextInput::make('beneficiary_swift')
                                ->label('Beneficiary SWIFT code')
                                ->inlineLabel()
                                ->placeholder('Please provide SWIFT code.')
                                ->required()
                                ->rule('regex:/^[A-Z0-9]{8}([A-Z0-9]{3})?$/') // 8 o 11 caracteres alfanuméricos
                                ->afterStateUpdated(fn ($state, callable $set) => 
                                    $set('beneficiary_swift', strtoupper($state))
                                )
                                ->helperText(fn (string $context) => in_array($context, ['create', 'edit']) 
                                ? '8 or 11 characters, e.g. DEUTDEFF500' 
                                : null), 
                                 
                            TextInput::make('beneficiary_acct_no')
                                ->label('Beneficiary Account Number')
                                ->inlineLabel()
                                ->placeholder('Please provide account number.')
                                ->required()
                                ->maxLength(255)
                                ->afterStateUpdated(fn ($state, callable $set) => $set('beneficiary_acct_no', ucwords(strtolower($state)))),
                        ])
                        ->columns(1)
                        ->collapsible(),

                    Forms\Components\Section::make('For Further Account Details')
                        ->schema([
                            TextInput::make('ffc_acct_name')
                                ->label('For Further Account Name')
                                ->inlineLabel()
                                ->placeholder('Please provide account name.')
                                ->required()
                                ->maxLength(255)
                                ->afterStateUpdated(fn ($state, callable $set) => $set('ffc_acct_name', ucwords(strtolower($state)))),

                            TextInput::make('ffc_acct_no')
                                ->label('For Further Account Number')
                                ->inlineLabel()
                                ->placeholder('Please provide account number.')
                                ->required()
                                ->unique()
                                ->maxLength(255)
                                ->afterStateUpdated(fn ($state, callable $set) => $set('ffc_acct_no', ucwords(strtolower($state)))),
                               
                            Textarea::make('ffc_acct_address')
                                ->label('For Further Account Address')
                                ->inlineLabel()
                                ->placeholder("Please provide adress")
                                ->required()
                                ->columnSpan('full')
                                ->afterStateUpdated(fn ($state, callable $set) => $set('ffc_acct_address', ucfirst(strtolower($state)))),
                                
                                
                        ])
                        ->columns(1)
                        ->collapsible(),
                
                
                
                
                //]),
                /* Forms\Components\Group::make() //Grupo 2
                ->schema([
                    Forms\Components\Section::make()
                    ->schema([

                    ])->columns(1)
                ]) */
                   
            ]);
    }           
                


    public static function table(Table $table): Table
    {
        return $table
            
            ->recordUrl(null)        // ❌ sin enlace
            ->recordAction(null)     // ❌ sin acción -> desactiva “edit” :contentReference[oaicite:0]{index=0}
            ->selectable(true)           // ✅ el clic pasa a ser un toggle de selección
            ->columns([           // usa el clic para seleccionar la fila
                //
                TextColumn::make('id')->sortable(),

                TextColumn::make('status_account')
                    ->label('Status')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('currency.name')
                    ->label('Currency')
                    ->sortable()
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('bank_inter.name')
                    ->label('Intermediary Bank')
                    ->sortable()
                    ->searchable()
                    ->toggleable(),
                
                TextColumn::make('bank.name')
                    ->label('Banks')
                    ->sortable()
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('beneficiary_acct_name')
                    ->label('Beneficiary Name')
                    ->sortable()
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('beneficiary_address')
                    ->label('Beneficiary Address')
                    ->sortable()
                    ->searchable()
                    ->toggleable()
                    ->wrap() // ✅ Permite que se haga multilínea
                    ->extraAttributes([
                        'class' => 'max-w-xl whitespace-normal', // ✅ Deja que el texto se envuelva
                    ]),

                TextColumn::make('beneficiary_swift')
                    ->label('Beneficiary Swift')
                    ->sortable()
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('beneficiary_acct_no')
                    ->label('Beneficiary Account')
                    ->sortable()
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('ffc_acct_name')
                    ->label('For Further Account Name')
                    ->sortable()
                    ->searchable()
                    ->toggleable()
                    ->wrap() // ✅ Permite que se haga multilínea
                    ->extraAttributes([
                        'class' => 'max-w-xl whitespace-normal', // ✅ Deja que el texto se envuelva
                    ]),

                TextColumn::make('ffc_acct_no')
                    ->label('For Further Account Number')
                    ->sortable()
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('ffc_acct_address')
                    ->label('For Further Account Address')
                    ->sortable()
                    ->searchable()
                    ->toggleable()
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
