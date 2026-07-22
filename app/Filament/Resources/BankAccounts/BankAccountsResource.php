<?php

namespace App\Filament\Resources\BankAccounts;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Actions\ActionGroup;
use Filament\Actions\ViewAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Resources\BankAccounts\Pages\ListBankAccounts;
use App\Filament\Resources\BankAccounts\Pages\CreateBankAccounts;
use App\Filament\Resources\BankAccounts\Pages\ViewBankAccounts;
use App\Filament\Resources\BankAccounts\Pages\EditBankAccounts;
use App\Filament\Resources\BankAccountsResource\Pages;
use App\Filament\Resources\BankAccountsResource\RelationManagers;
use App\Models\BankAccount;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Validation\Rules\Unique;
use Filament\Infolists\Components\TextEntry;

class BankAccountsResource extends Resource
{
    protected static ?string $model = BankAccount::class;
    //protected static ?string $cluster = Resources::class; // ✅ Vinculación correcta
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-minus';
    //protected static ?string $cluster = \App\Filament\Clusters\Resources::class;
    protected static string | \UnitEnum | null $navigationGroup = 'Banks';
    protected static ?int    $navigationSort  = 2;   // aparecerá primero

    /* ───── NUEVO: burbuja con el total en el menú ───── */
    public static function getNavigationBadge(): ?string
    {
        // Puedes usar self::$model::count() o Reinsurer::count()
        return BankAccount::count();
    }
   
    /*--------------------------------------------------------------
     | 1. Form New and Edit
     --------------------------------------------------------------*/
    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([

                //Forms\Components\Group::make() //Grupo 1
                //->schema([
                    Section::make('Wire Instructions')
                        ->columnSpanFull()
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
                                ->placeholder('Select currency.')
                                ->relationship(
                                    name: 'currency',
                                    titleAttribute: 'name',
                                    modifyQueryUsing: fn (Builder $query) => $query->orderBy('acronym')
                                )
                                ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->acronym} - {$record->name}")
                                ->searchable(['name', 'acronym']) // ✅ ahora "usd" sí encuentra
                                ->preload()
                                ->optionsLimit(1800)
                                ->required(),
                               
                            Select::make('intermediary_bank')
                                ->label('Intermediary Bank')
                                ->inlineLabel()
                                ->placeholder('Select intermediary bank')
                                ->relationship('bank','name')
                                ->searchable()
                                ->preload(),
                                
                               
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

                    
                    Section::make('Beneficiary Details')
                        ->columnSpanFull()
                        ->schema([
                            TextInput::make('beneficiary_acct_name')
                                ->label('Beneficiay Name')
                                ->inlineLabel()
                                ->placeholder("Please provide the beneficiary's full name")
                                ->maxLength(255),

                            Textarea::make('beneficiary_address')
                                ->label('Beneficiary Address')
                                ->inlineLabel()
                                ->placeholder("Please provide beneficiary adress")
                                ->columnSpan('full'),

                            TextInput::make('beneficiary_swift')
                                ->label('Beneficiary SWIFT code')
                                ->inlineLabel()
                                ->placeholder('Please provide SWIFT code.')
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
                                ->maxLength(255),
                        ])
                        ->columns(1)
                        ->collapsible(),

                    Section::make('For Further Account Details')
                        ->columnSpanFull()
                        ->schema([
                            TextInput::make('ffc_acct_name')
                                ->label('For Further Account Name')
                                ->inlineLabel()
                                ->placeholder('Please provide account name.')
                                ->required()
                                ->maxLength(255),

                            TextInput::make('ffc_acct_no')
                                ->label('For Further Account Number')
                                ->inlineLabel()
                                ->placeholder('Please provide account number.')
                                ->required()
                                ->unique(
                                    ignoreRecord: true,
                                    modifyRuleUsing: fn (Unique $rule) => $rule->whereNull('deleted_at')
                                )
                                ->maxLength(255),

                            Textarea::make('ffc_acct_address')
                                ->label('For Further Account Address')
                                ->inlineLabel()
                                ->placeholder("Please provide adress")
                                ->required()
                                ->columnSpan('full')
                                
                                
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
                





    




    /*--------------------------------------------------------------
     | 2. Infolist
    --------------------------------------------------------------*/
    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([

            Section::make('Wire Instructions')
            ->columnSpanFull()
            ->schema([
                \Filament\Schemas\Components\Grid::make(1)
                    ->extraAttributes(['style' => 'row-gap: 0;'])
                    ->schema([
                        \Filament\Schemas\Components\Grid::make(12)
                            ->extraAttributes(['style' => 'border-bottom:1px solid rgba(255,255,255,0.12); padding:2px 0;'])
                            ->schema([
                                TextEntry::make('status_label')
                                    ->hiddenLabel()->state('Status Account:')
                                    ->weight('bold')->alignment('right')
                                    ->columnSpan(3),

                                TextEntry::make('status_value')
                                    ->hiddenLabel()
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

                        \Filament\Schemas\Components\Grid::make(12)
                            ->extraAttributes(['style' => 'border-bottom:1px solid rgba(255,255,255,0.12); padding:2px 0;'])
                            ->schema([
                                TextEntry::make('currency_label')->hiddenLabel()->state('Currency:')
                                    ->weight('bold')->alignment('right')->columnSpan(3),
                                TextEntry::make('currency_value')->hiddenLabel()
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

                        \Filament\Schemas\Components\Grid::make(12)
                            ->extraAttributes(['style' => 'border-bottom:1px solid rgba(255,255,255,0.12); padding:2px 0;'])
                            ->schema([
                                TextEntry::make('ib_label')->hiddenLabel()->state('Intermediary Bank:')
                                    ->weight('bold')->alignment('right')->columnSpan(3),
                                TextEntry::make('ib_value')->hiddenLabel()
                                    ->state(function ($record) {
                                        return data_get($record, 'intermediaryBank.name')
                                            ?? data_get($record, 'intermediary_bank.name')
                                            ?? data_get($record, 'intermediary_bank')
                                            ?? '—';
                                    })
                                    ->columnSpan(9),
                            ]),

                        \Filament\Schemas\Components\Grid::make(12)
                            ->extraAttributes(['style' => 'border-bottom:1px solid rgba(255,255,255,0.12); padding:2px 0;'])
                            ->schema([
                                TextEntry::make('bank_label')->hiddenLabel()->state('Bank / For Credit to:')
                                    ->weight('bold')->alignment('right')->columnSpan(3),
                                TextEntry::make('bank_value')->hiddenLabel()
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

            Section::make('Beneficiary Details')
            ->columnSpanFull()
            ->schema([
                \Filament\Schemas\Components\Grid::make(1)->extraAttributes(['style' => 'row-gap: 0;'])->schema([
                    \Filament\Schemas\Components\Grid::make(12)->extraAttributes(['style' => 'border-bottom:1px solid rgba(255,255,255,0.12); padding:2px 0;'])->schema([
                        TextEntry::make('bname_label')->hiddenLabel()->state('Beneficiary Name:')
                            ->weight('bold')->alignment('right')->columnSpan(3),
                        TextEntry::make('bname_value')->hiddenLabel()
                            ->state(fn ($record) => $record->beneficiary_acct_name ?: '—')
                            ->columnSpan(9),
                    ]),
                    \Filament\Schemas\Components\Grid::make(12)->extraAttributes(['style' => 'border-bottom:1px solid rgba(255,255,255,0.12); padding:2px 0;'])->schema([
                        TextEntry::make('baddr_label')->hiddenLabel()->state('Beneficiary Address:')
                            ->weight('bold')->alignment('right')->columnSpan(3),
                        TextEntry::make('baddr_value')->hiddenLabel()
                            ->state(fn ($record) => $record->beneficiary_address ?: '—')
                            ->columnSpan(9),
                    ]),
                    \Filament\Schemas\Components\Grid::make(12)->extraAttributes(['style' => 'border-bottom:1px solid rgba(255,255,255,0.12); padding:2px 0;'])->schema([
                        TextEntry::make('bswift_label')->hiddenLabel()->state('Beneficiary SWIFT:')
                            ->weight('bold')->alignment('right')->columnSpan(3),
                        TextEntry::make('bswift_value')->hiddenLabel()
                            ->state(fn ($record) => $record->beneficiary_swift ? strtoupper($record->beneficiary_swift) : '—')
                            ->extraAttributes(['style' => 'font-family: ui-monospace, SFMono-Regular, Menlo, monospace; letter-spacing:0.5px;'])
                            ->columnSpan(9),
                    ]),
                    \Filament\Schemas\Components\Grid::make(12)->extraAttributes(['style' => 'border-bottom:1px solid rgba(255,255,255,0.12); padding:2px 0;'])->schema([
                        TextEntry::make('bacct_label')->hiddenLabel()->state('Beneficiary Account No.:')
                            ->weight('bold')->alignment('right')->columnSpan(3),
                        TextEntry::make('bacct_value')->hiddenLabel()
                            ->state(fn ($record) => $record->beneficiary_acct_no ?: '—')
                            ->extraAttributes(['style' => 'font-family: ui-monospace, SFMono-Regular, Menlo, monospace;'])
                            ->columnSpan(9),
                    ]),
                ]),
            ])->maxWidth('6xl')->collapsible(),

            Section::make('For Further Account Details')
            ->columnSpanFull()
            ->schema([
                \Filament\Schemas\Components\Grid::make(1)->extraAttributes(['style' => 'row-gap: 0;'])->schema([
                    \Filament\Schemas\Components\Grid::make(12)->extraAttributes(['style' => 'border-bottom:1px solid rgba(255,255,255,0.12); padding:2px 0;'])->schema([
                        TextEntry::make('ffcn_label')->hiddenLabel()->state('FFC Account Name:')
                            ->weight('bold')->alignment('right')->columnSpan(3),
                        TextEntry::make('ffcn_value')->hiddenLabel()
                            ->state(fn ($record) => $record->ffc_acct_name ?: '—')
                            ->columnSpan(9),
                    ]),
                    \Filament\Schemas\Components\Grid::make(12)->extraAttributes(['style' => 'border-bottom:1px solid rgba(255,255,255,0.12); padding:2px 0;'])->schema([
                        TextEntry::make('ffca_label')->hiddenLabel()->state('FFC Account No.:')
                            ->weight('bold')->alignment('right')->columnSpan(3),
                        TextEntry::make('ffca_value')->hiddenLabel()
                            ->state(fn ($record) => $record->ffc_acct_no ?: '—')
                            ->extraAttributes(['style' => 'font-family: ui-monospace, SFMono-Regular, Menlo, monospace;'])
                            ->columnSpan(9),
                    ]),
                    \Filament\Schemas\Components\Grid::make(12)->extraAttributes(['style' => 'border-bottom:1px solid rgba(255,255,255,0.12); padding:2px 0;'])->schema([
                        TextEntry::make('ffad_label')->hiddenLabel()->state('FFC Address:')
                            ->weight('bold')->alignment('right')->columnSpan(3),
                        TextEntry::make('ffad_value')->hiddenLabel()
                            ->state(fn ($record) => $record->ffc_acct_address ?: '—')
                            ->columnSpan(9),
                    ]),
                ]),
            ])->maxWidth('6xl')->collapsible(),

            Section::make('Audit Dates')
            ->columnSpanFull()
            ->schema([
                \Filament\Schemas\Components\Grid::make(12)->extraAttributes(['style' => 'border-bottom:1px solid rgba(255,255,255,0.12); padding:2px 0;'])->schema([
                    TextEntry::make('created_label')->hiddenLabel()->state('Created At:')->weight('bold')
                        ->alignment('right')->columnSpan(3),
                    TextEntry::make('created_value')->hiddenLabel()
                        ->state(fn ($record) => $record->created_at?->format('Y-m-d H:i') ?: '—')
                        ->columnSpan(9),
                ]),
                \Filament\Schemas\Components\Grid::make(12)->extraAttributes(['style' => 'border-bottom:1px solid rgba(255,255,255,0.12); padding:2px 0;'])->schema([
                    TextEntry::make('updated_label')->hiddenLabel()->state('Updated At:')->weight('bold')
                        ->alignment('right')->columnSpan(3),
                    TextEntry::make('updated_value')->hiddenLabel()
                        ->state(fn ($record) => $record->updated_at?->format('Y-m-d H:i') ?: '—')
                        ->columnSpan(9),
                ]),
            ])->maxWidth('6xl')->compact(),
        ]);
    }









    /*--------------------------------------------------------------
     | 3. CRUD Table
     --------------------------------------------------------------*/
    public static function table(Table $table): Table
    {
        return $table
            ->recordUrl(fn (BankAccount $record) => static::getUrl('view', ['record' => $record]))
            /* ->recordUrl(null)        // ❌ sin enlace
            ->recordAction(null)     // ❌ sin acción -> desactiva “edit” :contentReference[oaicite:0]{index=0}
            ->selectable(true)   */         // ✅ el clic pasa a ser un toggle de selección
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
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    DeleteAction::make(),
                    Action::make('generate_pdf')
                        ->label('PDF')
                        ->icon('heroicon-o-arrow-down-tray') // ícono de descarga
                        ->tooltip('Download PDF of this record')
                        ->color('gray') // o usa 'secondary', 'info', etc.
                        ->action(function ($record) {
                            // Aquí luego implementarás la lógica de generación de PDF
                            // Por ahora puede ser un log, redirección o notificación
                            Notification::make()
                                ->title('PDF generation triggered for: ' . $record->id)
                                ->success()
                                ->send();
                    }),
                ])
   
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
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
            'index'  => ListBankAccounts::route('/'),
            'create' => CreateBankAccounts::route('/create'),
            'view'   => ViewBankAccounts::route('/{record}'),   // 👈 NUEVA
            'edit'   => EditBankAccounts::route('/{record}/edit'),
        ];
    }
}
