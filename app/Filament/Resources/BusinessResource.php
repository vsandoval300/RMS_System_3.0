<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BusinessResource\Pages;
use App\Filament\Resources\BusinessResource\RelationManagers;
use App\Models\Business;
use App\Models\Reinsurer;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Illuminate\Support\Str;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Actions\Action;
use App\Filament\Resources\BusinessResource\Widgets;
use Filament\Notifications\Notification;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\OperativeDocsExport;
use App\Models\OperativeDoc;
use Filament\Pages\SubNavigationPosition;     
use Filament\Resources\Pages\Page; 
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\View as ViewField;
use Filament\Facades\Filament;
use App\Models\User;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Illuminate\Support\HtmlString;


// 👇 IMPORTS para INFOLIST
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section as InfoSection;
use Filament\Infolists\Components\ViewEntry;
use Filament\Infolists\Components\Grid as InfoGrid;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Split;
use Filament\Infolists\Components\Group;
use Filament\Infolists\Components\Tabs;
use Filament\Infolists\Components\Tabs\Tab;





class BusinessResource extends Resource
{
    protected static ?string $model = Business::class;

    protected static ?string $navigationIcon = 'heroicon-o-minus';
    protected static ?string $navigationGroup = 'Underwritten';
    protected static ?int    $navigationSort  = 10;   // aparecerá primero
    

     /* ───── NUEVO: burbuja con el total en el menú ───── */
    public static function getNavigationBadge(): ?string
    {
        return Business::count();
    } 

    public static function getTableQuery(): Builder
    {
        return Business::query()
            ->with([
                'reinsurer:id,short_name',
                'currency:id,acronym,name',
                'coverages:id,acronym,name',
                'renewedFrom:id,business_code',
            ])
            ->withCount([
                'operativeDocs',
            ]);
    }


    /* =========================
     *  FORM  (create / edit)
     * ========================= */
    public static function form(Form $form): Form
    {
        return $form
        // Inicio esquema principal (1)
        ->schema([ 
                

            // 🟡 BURBUJA PRINCIPAL
            Section::make()  // puedes ponerle un título general si quieres
                ->columns(3) 
                ->schema([

                Section::make('General Details')
                    ->compact() 
                    ->columns(4)    // ← aquí defines dos columnas
                    ->extraAttributes([
                        'class' => 'h-full',
                    ])
                    
                        ->schema([

                            Placeholder::make('')
                                ->content(''),      // vacío

                            Section::make('')
                                ->compact()
                                ->columns(4)
                                
                                    ->schema([ 



                                        Select::make('reinsurer_id')
                                            ->label('Reinsurer')
                                            ->relationship('reinsurer', 'name')
                                            ->required()
                                            ->searchable()
                                            ->preload()
                                            ->native(false)
                                            ->placeholder('Select a reinsurer')
                                            ->helperText(fn ($record) => $record ? 'Edit the value if necessary.' : '')
                                            ->columnSpan(2),

                                        TextInput::make('index')
                                            ->label('Index')
                                            //->inlineLabel()
                                            //->hiddenLabel()
                                            ->required()
                                            ->numeric()
                                            ->default(fn () => \App\Models\Business::max('index') + 1 ?? 1)
                                            ->disabledOn(['create', 'edit'])
                                            ->dehydrated()
                                            ->columnSpan(1),

                                        TextInput::make('business_code')
                                            ->label('Business Code')
                                            //->hiddenLabel()
                                            ->placeholder('Business code')
                                            ->disabled()
                                            ->dehydrated()
                                            ->required()
                                            ->unique(ignoreRecord: true)
                                            ->hiddenOn('create')
                                            ->columnSpan(1),


                                    ])
                                    ->columnSpan(4),    

                            Placeholder::make('')
                                ->content('')      // vacío
                                ->columnSpan(4),   // fuerza salto de fila       


                            Section::make('')
                                ->compact()
                                ->columns(4)

                                ->schema([

                                    Textarea::make('description')
                                        ->label('Description')
                                        //->hiddenLabel()
                                        ->placeholder('Fill in the business description')
                                        ->helperText(fn ($record) =>
                                            $record
                                                ? 'Update the business description if necessary.'
                                                : ''
                                        )
                                        ->required()
                                        //->columnSpanFull()
                                        ->rows(5) 
                                        ->columnSpan(4),

                                    Select::make('business_type')
                                        ->label('Business Type')
                                        //->hiddenLabel()
                                        //->inlineLabel()
                                        ->placeholder('Select a business type.') // 👈 Aquí cambias el texto
                                        ->options([
                                            'Own' => 'Own',
                                            'Third party' => 'Third party',
                                        ])
                                        //->default('Own')
                                        ->helperText(fn ($record) =>
                                            $record
                                                ? 'Edit the value if necessary.'
                                                : ''
                                        )
                                        ->required()
                                        ->searchable()
                                        ->columnSpan(2),   
                                        
                                    Select::make('purpose')
                                        ->label('Purpose')
                                        //->hiddenLabel()
                                        //->inlineLabel()
                                        ->placeholder('Select business purpose.') // 👈 Aquí cambias el texto
                                        ->options([
                                            'Traditional' => 'Traditional',
                                            'Strategic' => 'Strategic',
                                        ])
                                        //->default('Strategic')
                                        ->helperText(fn ($record) =>
                                            $record
                                                ? 'Edit the value if necessary.'
                                                : ''
                                        )
                                        ->required()
                                        ->searchable()
                                        ->columnSpan(2),  

                                    ])
                                    ->columnSpan(4),    
                                
                            Placeholder::make('')
                                ->content('')      // vacío
                                ->columnSpan(4),   // fuerza salto de fila   
                                
                                
                            Section::make('')
                                ->compact()
                                ->columns(2)
                                
                                    ->schema([ 
                            
                                        Select::make('parent_id')
                                            ->label('Treaty') // o "Master Contract", lo que prefieras
                                            ->relationship('treaty', 'treaty_code') // 👈 usa la nueva relación
                                            ->searchable()
                                            ->preload()
                                            ->optionsLimit(180)
                                            ->helperText(fn ($record) =>
                                                $record
                                                    ? 'Edit the value if necessary.'
                                                    : ''
                                            )
                                            ->nullable(),

                                        Select::make('renewed_from_id')
                                            ->label('Renewed From')
                                            //->inlineLabel()
                                            ->relationship('renewedFrom', 'business_code')
                                            ->searchable()
                                            ->preload()
                                            ->helperText(fn ($record) =>
                                                $record
                                                    ? 'Edit the value if necessary.'
                                                    : ''
                                            )
                                            ->nullable(),
                                    ])
                                    ->columnSpan(4),



                                Section::make('')
                                ->compact()
                                ->columns(2)
                                
                                    ->schema([ 
                            
                                        TextInput::make('source_code')
                                            ->label('Original id')
                                            ->dehydrated()
                                            ->placeholder('Enter original id if necessary.')
                                            ->columnSpan(1),
                                    ])
                                    ->columnSpan(4),    
                                    
                            Placeholder::make('')
                                ->content('')      // vacío
                                ->columnSpan(4),   // fuerza salto de fila   

                            Placeholder::make('')
                                ->content('')      // vacío
                                ->columnSpan(4),   // fuerza salto de fila     
                        
                        ])
                        ->columnSpan(2),




                 Section::make('Contract Attributes')
                    ->compact()
                    ->columns(1)
                    ->extraAttributes([
                        'class' => 'h-full min-h-[520px]', // ajusta 520px a tu caso
                    ])
                    
                        ->schema([ 

                            Placeholder::make('')
                                ->content(''),      // vacío

                            Select::make('reinsurance_type')
                                ->label('Contract Type')
                                ->placeholder('Select a reinsurer type')
                                ->options([
                                    'Facultative' => 'Facultative',
                                    'Treaty' => 'Treaty',
                                ])
                                ->default('Facultative')   // 👈 valor por defecto
                                ->helperText(fn ($record) =>
                                    $record
                                        ? 'Edit the value if necessary.'
                                        : 'You can keep the default value or choose a different one.'
                                )
                                //->disabled()
                                ->required()
                                ->searchable(),


                            Select::make('risk_covered')
                                ->label('Risk Covered')
                                //->hiddenLabel()
                                //->inlineLabel()
                                ->placeholder('Select the risk covered.') // 👈 Aquí cambias el texto
                                ->options([
                                    'Life' => 'Life',
                                    'Non-Life' => 'Non-Life',
                                ])
                                ->default('Non-Life')
                                ->helperText(fn ($record) =>
                                    $record
                                        ? 'Edit the value if necessary.'
                                        : 'You can keep the default value or choose a different one.'
                                )
                                ->required()
                                ->searchable(),   
                                
                            Select::make('premium_type')
                                ->label('Premium Type')
                                //->hiddenLabel()
                                //->inlineLabel()
                                ->placeholder('Select a premium type.') // 👈 Aquí cambias el texto
                                ->options([
                                    'Fixed' => 'Fixed',
                                    'Estimated' => 'Estimated',
                                ])
                                ->default('Fixed')
                                ->helperText(fn ($record) =>
                                    $record
                                        ? 'Edit the value if necessary.'
                                        : 'You can keep the default value or choose a different one.'
                                )
                                ->required()
                                ->searchable(),   
                                
                            Select::make('claims_type')
                                ->label('Claims Type')
                                //->hiddenLabel()
                                //->inlineLabel()
                                ->placeholder('Select claims type.') // 👈 Aquí cambias el texto
                                ->options([
                                    'Claims occurrence' => 'Claims occurrence',
                                    'Claims made' => 'Claims made',
                                ])
                                //->default('Claims occurrence')
                                ->helperText(fn ($record) =>
                                    $record
                                        ? 'Edit the value if necessary.'
                                        : ''
                                )
                                ->required()
                                ->searchable(),   
                                
                            Select::make('currency_id')
                                ->label('Currency')
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
                                ->default(157)
                                ->helperText(fn ($record) =>
                                    $record
                                        ? 'Edit the value if necessary.'
                                        : 'You can keep the default value or choose a different one.'
                                ) 
                                ->required(),    

                            Select::make('region_id')
                                ->label('Region')
                                //->hiddenLabel()
                                //->inlineLabel()
                                ->placeholder('Select business currency.') // 👈 Aquí cambias el texto
                                ->relationship('Region', 'name') // usa la relación en tu modelo
                                ->searchable()
                                ->preload()
                                //->default(2) 
                                ->helperText(fn ($record) =>
                                    $record
                                        ? 'Edit the value if necessary.'
                                        : ''
                                )
                                ->required(),  

                            Select::make('producer_id')
                                ->label('Producer')
                                //->hiddenLabel()
                                //->inlineLabel()
                                ->placeholder('Select business producer.') // 👈 Aquí cambias el texto
                                ->relationship('Producer', 'name') // usa la relación en tu modelo
                                ->searchable()
                                ->preload()
                                ->optionsLimit(300)
                                ->default(96)
                                ->helperText(fn ($record) =>
                                    $record
                                        ? 'Edit the value if necessary.'
                                        : 'You can keep the default value or choose a different one.'
                                )
                                ->required(),
                                

                            Placeholder::make('')
                                ->content(''),      // vacío
                                //->columnSpan(4),   // fuerza salto de fila 
                                
                            
                        ])
                         ->columnSpan(1),

                 
                Section::make('Status Tracking')
                    ->columns(3)
                    ->hidden(fn (string $context): bool => in_array($context, ['create', 'edit']))
                        ->schema([ 
                             Select::make('business_lifecycle_status')
                                ->label('Lifecycle Status')
                                ->options([
                                    'On Hold'   => 'On Hold',
                                    'In Force'  => 'In Force',
                                    'To Expire' => 'To Expire',
                                    'Expired'   => 'Expired',
                                    'Cancelled' => 'Cancelled',
                                ])
                                ->required()
                                ->default('On Hold')
                                ->native(false)   // UI bonita (TomSelect)
                                ->searchable()    // opcional
                                ->preload()       // opcional: carga todas las opciones
                                ->disabledOn(['create', 'edit']) // mismo comportamiento que tenías
                                ->dehydrated(),

                            TextInput::make('approval_status')
                                ->label('Approval Status')
                                //->hiddenLabel()
                                //->inlineLabel()
                                ->disabledOn(['create', 'edit'])
                                ->maxLength(510)
                                ->default('DFT'),

                            DatePicker::make('approval_status_updated_at')
                                ->label('Approval date')
                                //->hiddenLabel() 
                                ->disabledOn(['create', 'edit'])
                                //->inlineLabel(),
                            
                        ]),
                                    
                 ]),
                 
            ]);
            // Fin esquema principal (1)
    }










    /* =========================
     *  INFOLIST  (VIEW PAGE)
     * ========================= */
    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([

            InfoSection::make() // o InfoSection::make('Business Details')
            ->schema([

                /* ─────────────────────────  BUSINESS IDENTITY  ───────────────────────── */
                InfoSection::make('General Details')
                    ->compact()
                    ->schema([
                        InfoGrid::make(12)->schema([

                            // ✅ Columna izquierda (8)
                            InfoSection::make()
                                ->compact()
                                ->schema([



                                    InfoSection::make()
                                        ->compact()
                                        ->schema([
                                            InfoGrid::make(12)
                                            ->extraAttributes(['style' => 'gap:1px;padding:1px 0;'])
                                                ->schema([

                                                TextEntry::make('underwritten_by')
                                                    ->label('')
                                                    ->state(function ($record) {
                                                        $name = $record->reinsurer?->name ?? '—';

                                                        return new HtmlString(
                                                            "<strong>Underwritten by:</strong> {$name}"
                                                        );
                                                    })
                                                    ->columnSpan(8),

                                                TextEntry::make('business_code_entry')
                                                    ->label('')
                                                    ->state(function ($record) {
                                                        $code = $record->business_code ?: '—';

                                                        return new HtmlString(
                                                            "<strong>Business code:</strong> {$code}"
                                                        );
                                                    })
                                                    ->columnSpan(4),


                                            ]),

                                        ]),

                                    // Description
                                    InfoSection::make('Description')
                                        ->compact()
                                        ->schema([
                                            InfoGrid::make(12)
                                                ->extraAttributes(['style' => 'gap:1px;padding:1px 0;'])
                                                ->schema([
                                                    TextEntry::make('gd_desc_value')
                                                        ->label('')
                                                        ->state(fn ($record) => $record->description ?: '—')
                                                        ->extraAttributes(['style' => 'line-height:1;'])
                                                        ->columnSpan(12),
                                                ]),
                                        ]),

                                    InfoSection::make()
                                        ->compact()
                                        ->schema([
                                            InfoGrid::make(12)
                                            ->extraAttributes(['style' => 'gap:1px;padding:1px 0;'])
                                                ->schema([

                                                    TextEntry::make('business_type_entry')
                                                        ->label('')
                                                        ->state(function ($record) {
                                                            $value = $record->business_type ?: '—';

                                                            return new HtmlString(
                                                                "<strong>Business type:</strong> {$value}"
                                                            );
                                                        })
                                                        ->columnSpan(4),

                                                    TextEntry::make('purpose_entry')
                                                        ->label('')
                                                        ->state(function ($record) {
                                                            $value = $record->purpose ?: '—';

                                                            return new HtmlString(
                                                                "<strong>Purpose:</strong> {$value}"
                                                            );
                                                        })
                                                        ->columnSpan(4),


                                                ]),
                                        ]),

                                    InfoSection::make()
                                        ->compact()
                                        ->schema([
                                            InfoGrid::make(12)
                                            ->extraAttributes(['style' => 'gap:1px;padding:1px 0;'])
                                                ->schema([

                                                    TextEntry::make('parent_treaty_entry')
                                                        ->label('')
                                                        ->state(function ($record) {
                                                            $value = $record->parent?->treaty_code ?: '—';

                                                            return new HtmlString(
                                                                "<strong>Parent treaty:</strong> {$value}"
                                                            );
                                                        })
                                                        ->columnSpan(4),

                                                    TextEntry::make('renewed_from_entry')
                                                        ->label('')
                                                        ->state(function ($record) {
                                                            $value = $record->renewedFrom?->business_code ?: '—';

                                                            return new HtmlString(
                                                                "<strong>Renewed from:</strong> {$value}"
                                                            );
                                                        })
                                                        ->columnSpan(4),


                                                ]),
                                        ]),


                                    InfoSection::make()
                                        ->compact()
                                        ->schema([
                                            InfoGrid::make(12)
                                            ->extraAttributes(['style' => 'gap:1px;padding:1px 0;'])
                                                ->schema([

                                                    TextEntry::make('source_code')
                                                        ->label('')
                                                        ->state(function ($record) {
                                                            $value = $record->source_code ?: '—';

                                                            return new HtmlString(
                                                                "<strong>Original Id:</strong> {$value}"
                                                            );
                                                        })
                                                        ->columnSpan(4),



                                                ]),
                                        ]),    

                    ])
                    ->columnSpan(8),




                    /* ─────────────────────────  CONTRACT TERMS  ───────────────────────── */
                    InfoSection::make('Contract Attributes') // cambia el título si quieres
                        ->compact()
                        ->schema([
                            

                                TextEntry::make('reinsurance_type_entry')
                                    ->label('')
                                    ->state(function ($record) {
                                        $value = $record->reinsurance_type ?: '—';

                                        return new HtmlString(
                                            "<strong>Reinsurer type:</strong> {$value}"
                                        );
                                    })
                                    ->columnSpan(4)
                                    ->extraAttributes([
                                        'style' => 'gap:1px;padding:1px 0;border-bottom:1px solid rgba(255,255,255,.12);'
                                    ]),

                                TextEntry::make('risk_covered_entry')
                                    ->label('')
                                    ->state(function ($record) {
                                        $value = $record->risk_covered ?: '—';

                                        return new HtmlString(
                                            "<strong>Risk covered:</strong> {$value}"
                                        );
                                    })
                                    ->columnSpan(4)
                                    ->extraAttributes([
                                        'style' => 'gap:1px;padding:1px 0;border-bottom:1px solid rgba(255,255,255,.12);',
                                    ]),

                                TextEntry::make('premium_type_entry')
                                    ->label('')
                                    ->state(function ($record) {
                                        $value = $record->premium_type ?: '—';

                                        return new HtmlString(
                                            "<strong>Premium type:</strong> {$value}"
                                        );
                                    })
                                    ->columnSpan(4)
                                    ->extraAttributes([
                                        'style' => 'gap:1px;padding:1px 0;border-bottom:1px solid rgba(255,255,255,.12);',
                                    ]),

                                TextEntry::make('claims_type_entry')
                                    ->label('')
                                    ->state(function ($record) {
                                        $value = $record->claims_type ?: '—';

                                        return new HtmlString(
                                            "<strong>Claims type:</strong> {$value}"
                                        );
                                    })
                                    ->columnSpan(4)
                                    ->extraAttributes([
                                        'style' => 'gap:1px;padding:1px 0;border-bottom:1px solid rgba(255,255,255,.12);',
                                    ]),

                                TextEntry::make('currency_entry')
                                    ->label('')
                                    ->state(function ($record) {
                                        $value = $record->currency
                                            ? ($record->currency->acronym . ' - ' . $record->currency->name)
                                            : '—';

                                        return new HtmlString(
                                            "<strong>Currency:</strong> {$value}"
                                        );
                                    })
                                    ->columnSpan(4)
                                    ->extraAttributes([
                                        'style' => 'gap:1px;padding:1px 0;border-bottom:1px solid rgba(255,255,255,.12);',
                                    ]),

                                TextEntry::make('region_entry')
                                    ->label('')
                                    ->state(function ($record) {
                                        $value = $record->region?->name ?? '—';

                                        return new HtmlString(
                                            "<strong>Region:</strong> {$value}"
                                        );
                                    })
                                    ->columnSpan(4)
                                    ->extraAttributes([
                                        'style' => 'gap:1px;padding:1px 0;border-bottom:1px solid rgba(255,255,255,.12);',
                                    ]),

                                TextEntry::make('producer_entry')
                                    ->label('')
                                    ->state(function ($record) {
                                        $value = $record->producer?->name ?? '—';

                                        return new HtmlString(
                                            "<strong>Producer:</strong> {$value}"
                                        );
                                    })
                                    ->columnSpan(4)
                                    ->extraAttributes([
                                        'style' => 'gap:1px;padding:1px 0;border-bottom:1px solid rgba(255,255,255,.12);',
                                    ]),

      
                                    

                        ])
                        ->columnSpan(4),

                    ]),
                ]),

                
                


                /* ─────────────────────────  LIFECYCLE  ───────────────────────── */
                InfoSection::make('Lifecycle')
                    ->schema([
                        // 3 pares por fila → 12 cols / 4 = 3 columnas
                        InfoGrid::make(12)->schema([

        
                           TextEntry::make('approval_status_entry')
                                ->label('')
                                ->state(function ($record) {
                                    $status = $record->approval_status;

                                    $value = $status === null
                                        ? '—'
                                        : (method_exists($status, 'label')
                                            ? $status->label()
                                            : ($status->value ?? $status->name));

                                    $value = e($value);

                                    return new HtmlString(
                                        "<strong>Approval status:</strong> {$value}"
                                    );
                                })
                                ->columnSpan(2),

                            TextEntry::make('approval_date_entry')
                                ->label('')
                                ->state(function ($record) {
                                    $value = $record->approval_status_updated_at?->format('Y-m-d') ?: '—';

                                    return new HtmlString(
                                        "<strong>Approval date:</strong> {$value}"
                                    );
                                })
                                ->columnSpan(2),

                            TextEntry::make('lifecycle_status_entry')
                                ->label('')
                                ->state(function ($record) {
                                    $status = $record->business_lifecycle_status;

                                    $value = $status === null
                                        ? '—'
                                        : (method_exists($status, 'label')
                                            ? $status->label()
                                            : ($status->value ?? $status->name));

                                    $value = e($value);

                                    return new HtmlString(
                                        "<strong>Lifecycle status:</strong> {$value}"
                                    );
                                })
                                ->columnSpan(2),

                            TextEntry::make('created_at_entry')
                                ->label('')
                                ->state(function ($record) {
                                    $value = $record->created_at?->format('Y-m-d H:i') ?: '—';

                                    return new HtmlString(
                                        "<strong>Created at:</strong> {$value}"
                                    );
                                })
                                ->columnSpan(3),

                            TextEntry::make('created_by_user')
                                ->label('')
                                ->state(function ($record) {
                                        $value = $record->user->name;

                                        

                                        return new HtmlString(
                                            "<strong>Created by:</strong> {$value}"
                                        );
                                    })
                                ->columnSpan(3)    


                                                            
                        ]),
                    ]),
                //End InfoSection Lifecycle
                    
            ]),     
        ]);
    }
// End Infolist












// ╔═════════════════════════════════════════════════════════════════════════╗
// ║ Business Table                                                          ║
// ╚═════════════════════════════════════════════════════════════════════════╝

    public static function table(Table $table): Table
    {
        return $table
            ->columns([

                TextColumn::make('row_number')
                    ->label('#')
                    ->alignCenter()
                    ->state(function (Business $record) {
                        return Business::query()
                            ->where(function ($q) use ($record) {
                                $q->where('created_at', '<', $record->created_at)
                                ->orWhere(function ($q) use ($record) {
                                    $q->where('created_at', '=', $record->created_at)
                                        ->where('business_code', '<', $record->business_code); // 👈 desempate (ASC)
                                });
                            })
                            ->count() + 1;
                    })
                    ->alignCenter(),

                TextColumn::make('business_code')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('index')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('reinsurance_type')
                    ->searchable()
                    ->sortable(),
                    
                TextColumn::make('reinsurer.short_name')
                    ->label('Reinsurer')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('coverage_acronyms')
                    ->label('Coverages')
                    ->badge()
                    ->getStateUsing(fn (\App\Models\Business $record) =>
                        $record->coverages?->pluck('acronym')->filter()->unique()->values()->all() ?? []
                    )
                    ->tooltip(function (\App\Models\Business $record) {
                        if (! $record->coverages) {
                            return null;
                        }

                        $parts = $record->coverages
                            ->map(function ($c) {
                                $acronym = trim($c->acronym ?? '');
                                $name    = trim($c->name ?? '');

                                // agrega punto si no termina en . ! o ?
                                if ($name !== '' && ! preg_match('/[.!?]$/u', $name)) {
                                    $name .= '.';
                                }

                                return ($acronym !== '' && $name !== '') ? "{$acronym} = {$name}" : null;
                            })
                            ->filter()
                            ->values();

                        // Une cada par con un espacio
                        return $parts->join(' ');
                    })
                    ->searchable(query: function (Builder $query, string $search) {
                        $query->whereHas('coverages', fn ($q) =>
                            $q->where('acronym', 'like', "%{$search}%")
                            ->orWhere('name', 'like', "%{$search}%")
                        );
                    }),

              /*   TextColumn::make('renewed_from_id')
                    ->label('Renewed from')
                    ->searchable(), */

                TextColumn::make('renewed_from_id')
                    ->label('Renewed from')
                    ->placeholder('—')
                    ->url(function (?string $state) {
                        $code = is_string($state) ? trim($state) : null;

                        return filled($code)
                            ? BusinessResource::getUrl('view', ['record' => $code])
                            : null;
                    })
                    //->openUrlInNewTab() // opcional
                    ->searchable(),

                TextColumn::make('currency.acronym')
                    ->label('Currency')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('parent_id')
                    ->label('Treaty')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                    
                TextColumn::make('business_lifecycle_status')
                    ->label('Lifecycle')
                    ->badge()
                    ->color(fn ($state) => match ($state->value) {
                        'On Hold'   => 'gray',
                        'Pending'   => 'warning',
                        'In Force'  => 'success',
                        'To Expire' => 'info',
                        'Expired'   => 'danger',
                        'Cancelled' => 'gray',
                        default     => 'secondary',
                    })
                    ->searchable()
                    ->sortable(),

                TextColumn::make('operative_docs_count')
                    ->counts('operativeDocs')
                    ->label('Documents')
                    ->sortable()
                    ->formatStateUsing(fn ($state) => "$state document" . ($state === 1 ? '' : 's')) // 👈 esto agrega el texto
                    ->badge()
                    ->color(fn (int $state): string => $state > 0 ? 'primary' : 'gray'),



            ])
            
            ->filters([
                // 🔹 Filtro por Reinsurer
                SelectFilter::make('reinsurer_id')
                    ->label('Reinsurer')
                    ->relationship('reinsurer', 'short_name')
                    ->searchable()
                    ->preload(),

                // 🔹 Filtro por rango de fechas (created_at)
                Filter::make('created_at')
                    ->label('Created date')
                    ->form([
                        DatePicker::make('from')
                            ->label('From date'),
                        DatePicker::make('until')
                            ->label('To date'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'] ?? null,
                                fn (Builder $query, $date) =>
                                    $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['until'] ?? null,
                                fn (Builder $query, $date) =>
                                    $query->whereDate('created_at', '<=', $date),
                            );
                    }),
            ])



            
            // ╔═════════════════════════════════════════════════════════════════════════╗
            // ║ Underwritten Report                                                     ║
            // ╚═════════════════════════════════════════════════════════════════════════╝

            ->headerActions([
                Action::make('export')
                    ->label('Export Report')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->modalHeading('Export Reports')
                    ->modalSubmitActionLabel('Generate')
                    ->form([
                        Select::make('report_type')
                            ->label('Report Type')
                            ->options([
                                'operative_docs'     => 'Operative Docs (by Node Concept)',
                                'underwritten_report'=> 'Underwritten Report (by Deduction)',
                            ])
                            ->default('operative_docs')
                            ->required(),

                        // 🔹 Filtros
                        Select::make('reinsurer_ids')
                            ->label('Reinsurer(s)')
                            ->placeholder('All reinsurers')
                            ->options(fn () => \App\Models\Reinsurer::orderBy('name')->pluck('name', 'id'))
                            ->searchable()
                            ->preload()
                            ->multiple(),

                        DatePicker::make('from_date')->label('From date')->required(),
                        DatePicker::make('to_date')->label('To date')->required(),
                    ])
                    ->action(function (array $data) {

                        $from   = $data['from_date'] ?? null;
                        $to     = $data['to_date']   ?? null;
                        $report = $data['report_type'] ?? null;

                        if (!$from || !$to || !$report) {
                            Notification::make()->title('Please select report type and both dates.')->warning()->send();
                            return;
                        }

                        $reinsurerIds = collect($data['reinsurer_ids'] ?? [])->filter()->values();

                        $scope        = $reinsurerIds->isEmpty() ? 'all-reinsurers' : ('reinsurers-' . $reinsurerIds->implode('-'));
                        $reportLabels = [
                            'operative_docs'      => 'OperativeDocs_report',
                            'underwritten_report' => 'Underwritten_report',
                        ];
                        $reportLabel  = $reportLabels[$report] ?? $report;

                        $filename = sprintf(
                            '%s_%s_%s_to_%s.xlsx',
                            $reportLabel,
                            $scope,
                            Carbon::parse($from)->format('Ymd'),
                            Carbon::parse($to)->format('Ymd')
                        );

                        // 1) Consulta única con ambos conceptos disponibles
                        $flat = OperativeDoc::query()
                            ->with([
                                'business.reinsurer',
                                'business.currency',
                                'business.liabilityStructures',
                                'docType',
                            ])
                            ->whereDate('inception_date', '>=', $from)
                            ->whereDate('inception_date', '<=', $to)
                            ->join('businesses', 'operative_docs.business_code', '=', 'businesses.business_code')
                            ->when($reinsurerIds->isNotEmpty(), fn ($q) =>
                                $q->whereIn('businesses.reinsurer_id', $reinsurerIds)
                            )

                            // shares
                            ->leftJoin('businessdoc_schemes', 'businessdoc_schemes.op_document_id', '=', 'operative_docs.id')
                            ->leftJoin('cost_schemes', 'cost_schemes.id', '=', 'businessdoc_schemes.cscheme_id')

                            // insureds
                            ->leftJoin('businessdoc_insureds', 'businessdoc_insureds.op_document_id', '=', 'operative_docs.id')
                            ->leftJoin('companies', 'companies.id', '=', 'businessdoc_insureds.company_id')
                            ->leftJoin('countries', 'countries.id', '=', 'companies.country_id')
                            ->leftJoin('coverages', 'coverages.id', '=', 'businessdoc_insureds.coverage_id')

                            // cost nodes + partner
                            ->leftJoin('cost_nodesx', 'cost_nodesx.cscheme_id', '=', 'cost_schemes.id')
                            ->leftJoin('partners', 'partners.id', '=', 'cost_nodesx.partner_destination_id') 

                            // deductions (para el segundo reporte)
                            ->leftJoin('deductions', 'deductions.id', '=', 'cost_nodesx.concept')

                            ->orderBy('businesses.business_code')
                            ->select([
                                'operative_docs.*',

                                // campos “planos”
                                'cost_schemes.share as share',
                                'companies.name   as insured_name',
                                'countries.name   as country_name',
                                'coverages.name   as coverage_name',
                                'businessdoc_insureds.premium as insured_premium',

                                // nodos de costo
                                'partners.name           as partner_name',
                                'partners.acronym        as partner_acronym',
                                'cost_nodesx.concept     as node_concept',       // 👈 para OperativeDocsExport
                                'deductions.concept      as deduction_concept',  // 👈 para UnderwrittenReportExport
                                'cost_nodesx.value       as node_value',
                            ])
                            ->get();

                        if ($flat->isEmpty()) {
                            Notification::make()->title('No records found for the selected range.')->info()->send();
                            return;
                        }

                        // 2) Ramificación por tipo de reporte
                        if ($report === 'operative_docs') {
                            // Encabezados dinámicos por CONCEPTO del nodo
                            $partners = $flat->pluck('partner_acronym')->filter()->unique()->values();
                            $concepts = $flat->pluck('node_concept')->filter()->unique()->values();

                            // Pivot partner(node_acronym) × node_concept
                            $wide = $flat->groupBy('id')->map(function ($rows) {
                                $first = $rows->first();
                                $matrix = [];
                                foreach ($rows as $r) {
                                    if (!$r->partner_acronym || !$r->node_concept) continue;
                                    $p = $r->partner_acronym;
                                    $c = $r->node_concept;
                                    $matrix[$p][$c] = ($matrix[$p][$c] ?? 0) + (float) ($r->node_value ?? 0);
                                }
                                $first->pc_matrix = $matrix;
                                return $first;
                            })->values();

                            return Excel::download(
                                new \App\Exports\OperativeDocsExport($wide, $partners, $concepts),
                                $filename
                            );
                        }

                        if ($report === 'underwritten_report') {
                            // Encabezados dinámicos por CONCEPTO de deductions
                            $partners = $flat->pluck('partner_acronym')->filter()->unique()->values();
                            $concepts = $flat->pluck('deduction_concept')->filter()->unique()->values();

                            // Pivot partner(node_acronym) × deduction_concept
                            $wide = $flat->groupBy('id')->map(function ($rows) {
                                $first = $rows->first();
                                $matrix = [];
                                foreach ($rows as $r) {
                                    if (!$r->partner_acronym || !$r->deduction_concept) continue;
                                    $p = $r->partner_acronym;
                                    $c = $r->deduction_concept;
                                    $matrix[$p][$c] = ($matrix[$p][$c] ?? 0) + (float) ($r->node_value ?? 0);
                                }
                                $first->pc_matrix = $matrix;
                                return $first;
                            })->values();

                            return Excel::download(
                                new \App\Exports\UnderwrittenReportExport($wide, $partners, $concepts),
                                $filename
                            );
                        }

                        // Fallback (por si llega un valor inesperado)
                        Notification::make()->title('Unsupported report type.')->danger()->send();
                        return;
                    }),


            ])




            ->actions([
                Tables\Actions\ActionGroup::make([
                    // ───────── MAIN ─────────

                    Tables\Actions\ViewAction::make()
                        ->label('View')
                        //->color('primary')
                        ->url(fn (Business $record) =>
                            self::getUrl('view', ['record' => $record])
                        )
                        ->icon('heroicon-m-eye'),  // opcional

                    Tables\Actions\EditAction::make()
                        ->color('gray'),
                

               
                    // ───────── UPCOMING ─────────

                    Tables\Actions\Action::make('divider_1')
                        ->label('')
                        ->disabled()
                        ->extraAttributes([
                            'class' => 'pointer-events-none border-t border-gray-700 my-1',
                            'style' => 'height: 0; padding: 0; margin: 3px 0;',
                        ]),

                    Action::make('technical_result')
                        ->label('Technical result')
                        ->icon('heroicon-m-calculator')
                        ->color('primary')
                        ->disabled(function (): bool {
                            /** @var \App\Models\User|null $user */
                            $user = Filament::auth()->user();

                            return ! ($user?->can('business.technical_result') ?? false);
                        })
                        ->tooltip(function (): string {
                            /** @var \App\Models\User|null $user */
                            $user = Filament::auth()->user();

                            return ($user?->can('business.technical_result') ?? false)
                                ? 'View technical result'
                                : 'You do not have permission to access Technical Result';
                        })
                        ->action(function (): void {
                            /** @var \App\Models\User|null $user */
                            $user = Filament::auth()->user();

                            if (! ($user?->can('business.technical_result') ?? false)) {
                                Notification::make()
                                    ->title('Permission denied')
                                    ->body('You do not have permission to access Technical Result.')
                                    ->danger()
                                    ->send();

                                return;
                            }

                            Notification::make()
                                ->title('Technical result')
                                ->body('This feature is coming soon.')
                                ->info()
                                ->send();
                        }),

                    
                    Action::make('renewal')
                        ->label('Renewal')
                        ->icon('heroicon-m-arrow-path')
                        ->color('primary')
                        ->disabled(function (): bool {
                            /** @var \App\Models\User|null $user */
                            $user = Filament::auth()->user();

                            return ! ($user?->can('business.renewal') ?? false);
                        })
                        ->tooltip(function (): string {
                            /** @var \App\Models\User|null $user */
                            $user = Filament::auth()->user();

                            return ($user?->can('business.renewal') ?? false)
                                ? 'Renew this business'
                                : 'You do not have permission to renew this business';
                        })
                        ->action(function (): void {
                            /** @var \App\Models\User|null $user */
                            $user = Filament::auth()->user();

                            if (! ($user?->can('business.renewal') ?? false)) {
                                Notification::make()
                                    ->title('Permission denied')
                                    ->body('You do not have permission to renew this business.')
                                    ->danger()
                                    ->send();

                                return;
                            }

                            Notification::make()
                                ->title('Renewal')
                                ->body('This feature is coming soon.')
                                ->info()
                                ->send();
                        }),
                

                    // ───────── DANGER ─────────

                    Tables\Actions\Action::make('divider_1')
                        ->label('')
                        ->disabled()
                        ->extraAttributes([
                            'class' => 'pointer-events-none border-t border-gray-700 my-1',
                            'style' => 'height: 0; padding: 0; margin: 3px 0;',
                        ]),

                    Tables\Actions\DeleteAction::make(),
                ])
                

            ]);
            //->bulkActions([
                    //Tables\Actions\BulkActionGroup::make([
                    //Tables\Actions\DeleteBulkAction::make(),
             //   ]),
            //]);
    }

    public static function getRelations(): array
    {
        return [
            //
            RelationManagers\LiabilityStructuresRelationManager::class,
            RelationManagers\OperativeDocsRelationManager::class,        
        ];
    }



    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBusinesses::route('/'),
            'create' => Pages\CreateBusiness::route('/create'),
            'edit' => Pages\EditBusiness::route('/{record}/edit'),
            'view' => Pages\ViewBusiness::route('/{record}/view'), // 👈 Asegúrate que esto esté
            
        ];
    }


    public static function getWidgets(): array
    {
        return [
            Widgets\BusinessStatsOverview::class,
        ];
    }




}
