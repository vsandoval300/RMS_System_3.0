<?php

namespace App\Filament\Resources\ReinsurersResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;

class ReinsurerBankAccountsRelationManager extends RelationManager
{
    protected static string $relationship = 'reinsurerBankAccounts';

    protected static ?string $title       = 'Bank Accounts';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with([
                'bankAccount:
                id,
                beneficiary_acct_name,
                beneficiary_swift,
                beneficiary_acct_no,
                status_account,
                currency_id,
                bank_id',

                'bankAccount.currency:id,acronym',
                'bankAccount.bank:id,name',
            ]);
    }

    /* ─────────────────────────  FORM  ───────────────────────── */
    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('bank_account_id')
                ->label('Bank account')
                ->relationship('bankAccount', 'ffc_acct_no')   // 👈 campo seguro (no NULL)
                ->getOptionLabelFromRecordUsing(
                    fn ($record) => $record->ffc_acct_no
                                . ' - ' . ($record->bank?->name ?? 'No bank')
                                . ' - (' . ($record->ffc_acct_name ?? 'No FFC name') . ')'
                )
                ->searchable()
                ->preload()
                ->createOptionForm($this->bankAccountForm())
                ->required()
                ->columnSpanFull(), 
        ]);
    }



    /* sub-form para crear BankAccount al vuelo */
     protected static function bankAccountForm(): array
    {
        return [
            

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
                                ->optionsLimit(300)
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
                   
            
        ];
    } 





    /* ──────────────────────────  TABLE  ───────────────────────── */
    public function table(Table $table): Table
    {
        return $table
            ->persistSortInSession(false)     // evita re-usar un sort inválido
            ->persistFiltersInSession(false)  // idem filtros
            ->defaultSort('id', 'asc')        // sort seguro
            ->columns([
                
                TextColumn::make('index')
                    ->label('Index')
                    ->state(fn ($record, $rowLoop) => $rowLoop->iteration)
                    ->sortable(false) // 👈 no tiene sentido ordenar este índice
                    ->searchable(false), // 👈 tampoco buscarlo
                
                
                
                TextColumn::make('ffc_acct_name')
                    ->label('Owner')
                    ->state(fn ($record) =>
                        $record->bankAccount?->ffc_acct_name ?? '—'
                    ),

                TextColumn::make('ffc_acct_no')
                    ->label('Account #')
                    ->state(fn ($record) =>
                        $record->bankAccount?->ffc_acct_no ?? '—'
                    )
                    ->toggleable(),

                TextColumn::make('cur')
                    ->label('Currency')
                    ->state(fn ($record) =>
                        $record->bankAccount?->currency?->acronym ?? '—'
                    ),

                TextColumn::make('bankAccount.bank.name')
                    ->label('Bank')
                    ->state(fn ($record) =>
                        $record->bankAccount?->bank?->name ?? '—'
                ),

                TextColumn::make('bankAccount.status_account')
                    ->label('Status')
                    ->badge()
                    ->color(fn ($state) =>
                        strcasecmp($state, 'Active') === 0 ? 'success' : 'secondary'
                    )
                    ->toggleable(),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Add Bank Account'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }
}
