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

// 👇 IMPORTS para INFOLIST
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section as InfoSection;
use Filament\Infolists\Components\Grid as InfoGrid;
use Filament\Infolists\Components\TextEntry;

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
                





    





public static function infolist(Infolist $infolist): Infolist
{
    return $infolist->schema([

        InfoSection::make('Wire Instructions')->schema([
            InfoGrid::make(1)
                ->extraAttributes(['style' => 'row-gap: 0;'])
                ->schema([
                    InfoGrid::make(12)
                        ->extraAttributes(['style' => 'border-bottom:1px solid rgba(255,255,255,0.12); padding:2px 0;'])
                        ->schema([
                            TextEntry::make('status_label')
                                ->label('')->state('Status Account:')
                                ->weight('bold')->alignment('right')
                                ->columnSpan(3),

                            TextEntry::make('status_value')
                                ->label('')
                                // usa el valor crudo para poder colorear; el “—” lo ponemos en formatState
                                ->state(fn ($record) => $record->status_account)
                                ->formatStateUsing(fn ($state) => filled($state) ? $state : '—')
                                ->badge()
                                ->color(function ($state) {
                                    if (blank($state)) return 'gray';
                                    return match (strtolower($state)) {
                                        'active'   => 'primary',
                                        'inactive' => 'danger',
                                        default    => 'gray',
                                    };
                                })
                                ->columnSpan(9),
                        ]),

                    InfoGrid::make(12)
                        ->extraAttributes(['style' => 'border-bottom:1px solid rgba(255,255,255,0.12); padding:2px 0;'])
                        ->schema([
                            TextEntry::make('currency_label')->label('')->state('Currency:')
                                ->weight('bold')->alignment('right')->columnSpan(3),
                            TextEntry::make('currency_value')->label('')
                                ->state(function ($record) {
                                    $cur = data_get($record, 'currency');
                                    if ($cur && (data_get($cur, 'acronym') || data_get($cur, 'name'))) {
                                        return trim(($cur->acronym ?? '') . ' - ' . ($cur->name ?? ''), ' -') ?: '-';
                                    }
                                    $acronym = data_get($record, 'currency_acronym');
                                    $name    = data_get($record, 'currency_name');
                                    return ($acronym || $name) ? trim(($acronym ?? '') . ' - ' . ($name ?? ''), ' -') : '—';
                                })
                                ->columnSpan(9),
                        ]),

                    InfoGrid::make(12)
                        ->extraAttributes(['style' => 'border-bottom:1px solid rgba(255,255,255,0.12); padding:2px 0;'])
                        ->schema([
                            TextEntry::make('ib_label')->label('')->state('Intermediary Bank:')
                                ->weight('bold')->alignment('right')->columnSpan(3),
                            TextEntry::make('ib_value')->label('')
                                ->state(function ($record) {
                                    return data_get($record, 'intermediaryBank.name')
                                        ?? data_get($record, 'intermediary_bank.name')
                                        ?? data_get($record, 'intermediary_bank')
                                        ?? '—';
                                })
                                ->columnSpan(9),
                        ]),

                    InfoGrid::make(12)
                        ->extraAttributes(['style' => 'border-bottom:1px solid rgba(255,255,255,0.12); padding:2px 0;'])
                        ->schema([
                            TextEntry::make('bank_label')->label('')->state('Bank / For Credit to:')
                                ->weight('bold')->alignment('right')->columnSpan(3),
                            TextEntry::make('bank_value')->label('')
                                ->state(function ($record) {
                                    return data_get($record, 'bank.name')
                                        ?? data_get($record, 'bank_id.name')
                                        ?? data_get($record, 'bank')
                                        ?? '—';
                                })
                                ->columnSpan(9),
                        ]),
                ]),
        ])->maxWidth('6xl')->collapsible(),

        InfoSection::make('Beneficiary Details')->schema([
            InfoGrid::make(1)->extraAttributes(['style' => 'row-gap: 0;'])->schema([
                InfoGrid::make(12)->extraAttributes(['style' => 'border-bottom:1px solid rgba(255,255,255,0.12); padding:2px 0;'])->schema([
                    TextEntry::make('bname_label')->label('')->state('Beneficiary Name:')
                        ->weight('bold')->alignment('right')->columnSpan(3),
                    TextEntry::make('bname_value')->label('')
                        ->state(fn ($record) => $record->beneficiary_acct_name ?: '—')
                        ->columnSpan(9),
                ]),
                InfoGrid::make(12)->extraAttributes(['style' => 'border-bottom:1px solid rgba(255,255,255,0.12); padding:2px 0;'])->schema([
                    TextEntry::make('baddr_label')->label('')->state('Beneficiary Address:')
                        ->weight('bold')->alignment('right')->columnSpan(3),
                    TextEntry::make('baddr_value')->label('')
                        ->state(fn ($record) => $record->beneficiary_address ?: '—')
                        ->columnSpan(9),
                ]),
                InfoGrid::make(12)->extraAttributes(['style' => 'border-bottom:1px solid rgba(255,255,255,0.12); padding:2px 0;'])->schema([
                    TextEntry::make('bswift_label')->label('')->state('Beneficiary SWIFT:')
                        ->weight('bold')->alignment('right')->columnSpan(3),
                    TextEntry::make('bswift_value')->label('')
                        ->state(fn ($record) => $record->beneficiary_swift ? strtoupper($record->beneficiary_swift) : '—')
                        ->extraAttributes(['style' => 'font-family: ui-monospace, SFMono-Regular, Menlo, monospace; letter-spacing:0.5px;'])
                        ->columnSpan(9),
                ]),
                InfoGrid::make(12)->extraAttributes(['style' => 'border-bottom:1px solid rgba(255,255,255,0.12); padding:2px 0;'])->schema([
                    TextEntry::make('bacct_label')->label('')->state('Beneficiary Account No.:')
                        ->weight('bold')->alignment('right')->columnSpan(3),
                    TextEntry::make('bacct_value')->label('')
                        ->state(fn ($record) => $record->beneficiary_acct_no ?: '—')
                        ->extraAttributes(['style' => 'font-family: ui-monospace, SFMono-Regular, Menlo, monospace;'])
                        ->columnSpan(9),
                ]),
            ]),
        ])->maxWidth('6xl')->collapsible(),

        InfoSection::make('For Further Account Details')->schema([
            InfoGrid::make(1)->extraAttributes(['style' => 'row-gap: 0;'])->schema([
                InfoGrid::make(12)->extraAttributes(['style' => 'border-bottom:1px solid rgba(255,255,255,0.12); padding:2px 0;'])->schema([
                    TextEntry::make('ffcn_label')->label('')->state('FFC Account Name:')
                        ->weight('bold')->alignment('right')->columnSpan(3),
                    TextEntry::make('ffcn_value')->label('')
                        ->state(fn ($record) => $record->ffc_acct_name ?: '—')
                        ->columnSpan(9),
                ]),
                InfoGrid::make(12)->extraAttributes(['style' => 'border-bottom:1px solid rgba(255,255,255,0.12); padding:2px 0;'])->schema([
                    TextEntry::make('ffca_label')->label('')->state('FFC Account No.:')
                        ->weight('bold')->alignment('right')->columnSpan(3),
                    TextEntry::make('ffca_value')->label('')
                        ->state(fn ($record) => $record->ffc_acct_no ?: '—')
                        ->extraAttributes(['style' => 'font-family: ui-monospace, SFMono-Regular, Menlo, monospace;'])
                        ->columnSpan(9),
                ]),
                InfoGrid::make(12)->extraAttributes(['style' => 'border-bottom:1px solid rgba(255,255,255,0.12); padding:2px 0;'])->schema([
                    TextEntry::make('ffad_label')->label('')->state('FFC Address:')
                        ->weight('bold')->alignment('right')->columnSpan(3),
                    TextEntry::make('ffad_value')->label('')
                        ->state(fn ($record) => $record->ffc_acct_address ?: '—')
                        ->columnSpan(9),
                ]),
            ]),
        ])->maxWidth('6xl')->collapsible(),

        InfoSection::make('Audit Dates')->schema([
            InfoGrid::make(12)->extraAttributes(['style' => 'border-bottom:1px solid rgba(255,255,255,0.12); padding:2px 0;'])->schema([
                TextEntry::make('created_label')->label('')->state('Created At:')->weight('bold')
                    ->alignment('right')->columnSpan(3),
                TextEntry::make('created_value')->label('')
                    ->state(fn ($record) => $record->created_at?->format('Y-m-d H:i') ?: '—')
                    ->columnSpan(9),
            ]),
            InfoGrid::make(12)->extraAttributes(['style' => 'border-bottom:1px solid rgba(255,255,255,0.12); padding:2px 0;'])->schema([
                TextEntry::make('updated_label')->label('')->state('Updated At:')->weight('bold')
                    ->alignment('right')->columnSpan(3),
                TextEntry::make('updated_value')->label('')
                    ->state(fn ($record) => $record->updated_at?->format('Y-m-d H:i') ?: '—')
                    ->columnSpan(9),
            ]),
        ])->maxWidth('6xl')->compact(),
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
