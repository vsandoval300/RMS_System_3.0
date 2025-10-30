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
use Filament\Tables\Columns\BadgeColumn;


// 👇 IMPORTS para INFOLIST
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section as InfoSection;
use Filament\Infolists\Components\Grid as InfoGrid;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Split;
use Filament\Infolists\Components\Group;



class BusinessResource extends Resource
{
    protected static ?string $model = Business::class;

    protected static ?string $navigationIcon = 'heroicon-o-minus';
    protected static ?string $navigationGroup = 'Underwritten';
    protected static ?int    $navigationSort  = 9;   // aparecerá primero
    

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
            ])
            ->withCount([
                'operativeDocs',
            ]);
    }



    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Section::make('General Details')
                    ->columns(3)    // ← aquí defines dos columnas
                    
                    ->schema([
                                Section::make()
                                        ->columns(1) // subdivide la columna 3 en 2
                                        ->schema([
                                            Select::make('reinsurer_id')
                                            ->label('Reinsurer')
                                            //->hiddenLabel()
                                            ->relationship('reinsurer', 'name')
                                            ->required()
                                            ->searchable()
                                            ->preload() // 👈 fuerza la carga inmediata de los options
                                            ->native(false)
                                            ->placeholder('Select a reinsurer')
                                            ->live(onBlur: true)
                                            ->afterStateUpdated(function (string $operation, $state, Forms\Set $set) {
                                                if ($operation !== 'create' || !$state) {
                                                    return;
                                                }

                                                $reinsurer = Reinsurer::find($state);

                                                if (! $reinsurer) {
                                                    return;
                                                }

                                                $year = Carbon::now()->format('Y');
                                                $acronym = Str::upper($reinsurer->acronym);
                                                $number = str_pad($reinsurer->cns_reinsurer ?? $reinsurer->id, 3, '0', STR_PAD_LEFT);

                                                $prefix = "{$year}-{$acronym}{$number}";

                                                // Buscar el último código existente que empiece con ese prefijo
                                                $lastBusiness = Business::query()
                                                    ->withTrashed() // 👈 incluye borrados (deleted_at no null)
                                                    ->where('business_code', 'like', "$prefix-%")
                                                    ->orderByDesc('business_code')
                                                    ->first();

                                                // Extraer el consecutivo y sumarle 1
                                                $lastNumber = 0;

                                                if ($lastBusiness && preg_match('/-(\d{3})$/', $lastBusiness->business_code, $matches)) {
                                                    $lastNumber = (int)$matches[1];
                                                }

                                                $consecutive = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);

                                                $businessCode = "{$prefix}-{$consecutive}";

                                                $set('business_code', $businessCode);
                                            })
                                            //->columnSpan(2),
                                        ])
                                        ->columnSpan(2),
                                
                                        Section::make()
                                            ->columns(2) // subdivide la columna 3 en 2
                                            ->schema([
                                                TextInput::make('index')
                                                ->label('Index')
                                                //->inlineLabel()
                                                //->hiddenLabel()
                                                ->required()
                                                ->numeric()
                                                ->default(fn () => \App\Models\Business::max('index') + 1 ?? 1)
                                                ->disabledOn(['create', 'edit'])
                                                ->dehydrated(),
                                                

                                                TextInput::make('business_code')
                                                ->label('Business Code')
                                                //->hiddenLabel()
                                                ->placeholder('Business code')
                                                ->disabled()
                                                ->dehydrated()
                                                ->required()
                                                ->unique(ignoreRecord: true),
                                                
                                         ])
                                         ->columnSpan(1), 

                                Section::make()
                                    ->columns(3) // subdivide la columna 3 en 2
                                    ->schema([
                                        Textarea::make('description')
                                        ->label('Description')
                                        //->hiddenLabel()
                                        ->placeholder('Fill in the business description')
                                        ->required()
                                        ->columnSpanFull()
                                        ->rows(3), 
                                    ])
                                    //->columnSpan(1), 
                                  
                                
                                
                       
                            ]),

                


                   Section::make('Contract Details')
                    ->columns(3)
                    
                        ->schema([
                    
                        
                            Select::make('reinsurance_type')
                                ->label('Contract Type')
                                //->hiddenLabel()
                                //->inlineLabel()
                                ->placeholder('Select a reinsurer type') // 👈 Aquí cambias el texto
                                ->options([
                                    'Facultative' => 'Facultative',
                                    'Treaty' => 'Treaty',
                                ])
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
                                ->required()
                                ->searchable(),
                            
                            Select::make('business_type')
                                ->label('Business Type')
                                //->hiddenLabel()
                                //->inlineLabel()
                                ->placeholder('Select a business type.') // 👈 Aquí cambias el texto
                                ->options([
                                    'Own' => 'Own',
                                    'Third party' => 'Third party',
                                ])
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
                                ->required()
                                ->searchable(),

                            Select::make('purpose')
                                ->label('Purpose')
                                //->hiddenLabel()
                                //->inlineLabel()
                                ->placeholder('Select business purpose.') // 👈 Aquí cambias el texto
                                ->options([
                                    'Normal' => 'Normal',
                                    'Strategic' => 'Strategic',
                                ])
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
                                ->required()
                                ->searchable(),

                            Select::make('producer_id')
                                ->label('Producer')
                                //->hiddenLabel()
                                //->inlineLabel()
                                 ->placeholder('Select business producer.') // 👈 Aquí cambias el texto
                                ->relationship('Producer', 'name') // usa la relación en tu modelo
                                ->searchable()
                                ->preload()
                                ->optionsLimit(300)
                                ->required(),

                            Select::make('currency_id')
                                ->label('Currency')
                                //->hiddenLabel()
                                //->inlineLabel()
                                ->placeholder('Business region.') // 👈 Aquí cambias el texto
                                ->relationship(
                                    name: 'currency',         // ← relación en tu modelo
                                    titleAttribute: 'name')
                                ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->acronym} - {$record->name}")
                                ->searchable()
                                ->preload()
                                ->optionsLimit(180)
                                ->required(),

                            Select::make('region_id')
                                ->label('Region')
                                //->hiddenLabel()
                                //->inlineLabel()
                                ->placeholder('Select business currency.') // 👈 Aquí cambias el texto
                                ->relationship('Region', 'name') // usa la relación en tu modelo
                                ->searchable()
                                ->preload()
                                ->required(),
                    
                 ]),   // ← cierra schema() y luego la Sección

                 
                Section::make('Relationship Info')
                    ->columns(2)
                    
                        ->schema([ 
                 
                            Select::make('parent_id')
                                ->label('Parent Business')
                                //->inlineLabel()
                                ->relationship('parent', 'business_code')
                                ->searchable()
                                ->preload()
                                ->optionsLimit(180)
                                ->nullable(),

                            Select::make('renewed_from_id')
                                ->label('Renewed From')
                                //->inlineLabel()
                                ->relationship('renewedFrom', 'business_code')
                                ->searchable()
                                ->preload()
                                ->nullable(),
                        ]),
                 
                Section::make('Status Tracking')
                    ->columns(3)
                    ->hidden(fn (string $context): bool => $context === 'create')
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
                                ->disabledOn(['create']) // mismo comportamiento que tenías
                                ->dehydrated(),

                            TextInput::make('approval_status')
                                ->label('Approval Status')
                                //->hiddenLabel()
                                //->inlineLabel()
                                ->disabledOn(['create'])
                                ->maxLength(510)
                                ->default('DFT'),

                            DatePicker::make('approval_status_updated_at')
                                ->label('Approval date')
                                //->hiddenLabel() 
                                ->disabledOn(['create']),
                                //->inlineLabel(),
                            

                        ]),
                  
                 Section::make('Audit Info')
                    ->schema([
                        Placeholder::make('created_at')
                            ->label('Created at')
                            ->content(fn ($record) => $record?->created_at?->format('d/m/Y H:i:s')),

                        Placeholder::make('updated_at')
                            ->label('Last updated')
                            ->content(fn ($record) => $record?->updated_at?->format('d/m/Y H:i:s')),
                    ])
                    ->columns(2)
                    ->hidden(fn (string $context): bool => $context === 'create') 
                 
                 
                 

            ]);
    }










    


    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([

            /* ─────────────────────────  GENERAL DETAILS  ───────────────────────── */
            InfoSection::make('Entity & Code')
                ->compact()
                ->schema([
                    InfoGrid::make(12)->schema([

                        // Fila 1: Reinsurer (izq)  |  Business Code (der)
                        Split::make([
                            TextEntry::make('gd_reinsurer_label')->label('Underwritten by')
                                ->weight('bold')->alignment('left'),   
                            TextEntry::make('gd_reinsurer_value')->label('')
                                ->state(fn ($record) => $record->reinsurer?->name ?? '—'),
                        ])->columnSpan(4),
                        //->extraAttributes(['style' => 'gap:12px;padding:8px 0;border-bottom:1px solid rgba(255,255,255,.12);']),


                        Split::make([
                            TextEntry::make('gd_code_label')->label('')->state('')
                                ->weight('bold')->alignment('left'),
                            TextEntry::make('')->label('')
                                ->state(fn ($record) => ''),
                        ])->columnSpan(4),
                        //->extraAttributes(['style' => 'gap:12px;padding:8px 0;border-bottom:1px solid rgba(255,255,255,.12);']),


                        Split::make([
                            TextEntry::make('gd_code_label')->label('')->state('  Business code')
                                ->weight('bold')->alignment('left'),
                            TextEntry::make('gd_code_value')->label('')
                                ->state(fn ($record) => $record->business_code ?: '—'),
                        ])->columnSpan(4),
                        //->extraAttributes(['style' => 'gap:12px;padding:8px 0;border-bottom:1px solid rgba(255,255,255,.12);']),
                        
                    ]),
                ])
                ->maxWidth('7xl'),
                //->collapsible(),

            
            /* ─────────────────────────  DESCRIPTION  ───────────────────────── */    
            InfoSection::make('Context')
                ->compact()
                ->schema([
                    // Fila: Description (2 | 8)
                    InfoGrid::make(12)
                        ->extraAttributes(['style' => 'gap:1px;padding:1px 0;'])
                        ->schema([
                            TextEntry::make('gd_desc_label')
                                ->label('')
                                ->state('  General description')
                                ->weight('bold')
                                ->alignment('left')
                                ->columnSpan(2),

                            TextEntry::make('gd_desc_value')
                                ->label('')
                                ->state(fn ($record) => $record->description ?: '—')
                                ->extraAttributes(['style' => 'line-height:1;'])
                                ->columnSpan(8),
                            // (Opcional) Dejar 2 cols vacías o añade un spacer si lo prefieres
                            // TextEntry::make('gd_desc_spacer')->label('')->state('')->columnSpan(2)->hiddenLabel(),
                        ]),
                ])
                ->maxWidth('7xl'),
                //->collapsible(),


            /* ─────────────────────────  CONTRACT DETAILS  ───────────────────────── */
            InfoSection::make('Contract Details')
                ->schema([
                    // 3 pares por fila → 12 cols / 4 = 3 columnas
                    InfoGrid::make(12)->schema([

                        /* Fila 1 */
                        Split::make([
                            TextEntry::make('rt_label')->label('')->state('Reinsurer type')
                                ->weight('bold')->alignment('left'),
                            TextEntry::make('rt_value')->label('')
                                ->state(fn ($record) => $record->reinsurance_type ?: '—'),
                        ])->columnSpan(4)->extraAttributes(['style' => 'gap:1px;padding:1px 0;border-bottom:1px solid rgba(255,255,255,.12);']),

                        Split::make([
                            TextEntry::make('ct_label')->label('')->state('Claims type')
                                ->weight('bold')->alignment('left'),
                            TextEntry::make('ct_value')->label('')
                                ->state(fn ($record) => $record->claims_type ?: '—'),
                        ])->columnSpan(4)->extraAttributes(['style' => 'gap:1px;padding:1px 0;border-bottom:1px solid rgba(255,255,255,.12);']),

                        Split::make([
                            TextEntry::make('parent_label')->label('')->state('Parent business')
                                ->weight('bold')->alignment('left'),
                            TextEntry::make('parent_value')->label('')
                                ->state(fn ($record) => $record->parent?->business_code ?: '—'),
                    ])->columnSpan(4)->extraAttributes(['style' => 'gap:1px;padding:1px 0;border-bottom:1px solid rgba(255,255,255,.12);']),

                        /* Fila 2 */
                        Split::make([
                            TextEntry::make('rc_label')->label('')->state('Risk covered')
                                ->weight('bold')->alignment('left'),
                            TextEntry::make('rc_value')->label('')
                                ->state(fn ($record) => $record->risk_covered ?: '—'),
                        ])->columnSpan(4)->extraAttributes(['style' => 'gap:1px;padding:1px 0;border-bottom:1px solid rgba(255,255,255,.12);']),

                        Split::make([
                            TextEntry::make('prod_label')->label('')->state('Producer')
                                ->weight('bold')->alignment('left'),
                            TextEntry::make('prod_value')->label('')
                                ->state(fn ($record) => $record->producer?->name ?? '—'),
                        ])->columnSpan(4)->extraAttributes(['style' => 'gap:1px;padding:1px 0;border-bottom:1px solid rgba(255,255,255,.12);']),

                        Split::make([
                            TextEntry::make('renew_label')->label('')->state('Renewed from')
                                ->weight('bold')->alignment('left'),
                            TextEntry::make('renew_value')->label('')
                                ->state(fn ($record) => $record->renewedFrom?->business_code ?: '—'),
                        ])->columnSpan(4)->extraAttributes(['style' => 'gap:1px;padding:1px 0;border-bottom:1px solid rgba(255,255,255,.12);']),

                        /* Fila 3 */
                        Split::make([
                            TextEntry::make('bt_label')->label('')->state('Business type')
                                ->weight('bold')->alignment('left'),
                            TextEntry::make('bt_value')->label('')
                                ->state(fn ($record) => $record->business_type ?: '—'),
                        ])->columnSpan(4)->extraAttributes(['style' => 'gap:1px;padding:1px 0;border-bottom:1px solid rgba(255,255,255,.12);']),

                        Split::make([
                            TextEntry::make('curr_label')->label('')->state('Currency')
                                ->weight('bold')->alignment('left'),
                            TextEntry::make('curr_value')->label('')
                                ->state(fn ($record) => $record->currency
                                    ? ($record->currency->acronym . ' - ' . $record->currency->name)
                                    : '—'),
                        ])->columnSpan(4)->extraAttributes(['style' => 'gap:1px;padding:1px 0;border-bottom:1px solid rgba(255,255,255,.12);']),

                        Split::make([
                            TextEntry::make('appr_label')->label('')->state('Approval status')
                                ->weight('bold')->alignment('left'),
                            TextEntry::make('appr_value')->label('')
                                ->state(fn ($record) => $record->approval_status ?: '—'),
                        ])->columnSpan(4)->extraAttributes(['style' => 'gap:1px;padding:1px 0;border-bottom:1px solid rgba(255,255,255,.12);']),

                        /* Fila 4 */
                        Split::make([
                            TextEntry::make('pt_label')->label('')->state('Premium type')
                                ->weight('bold')->alignment('left'),
                            TextEntry::make('pt_value')->label('')
                                ->state(fn ($record) => $record->premium_type ?: '—'),
                        ])->columnSpan(4)->extraAttributes(['style' => 'gap:1px;padding:1px 0;border-bottom:1px solid rgba(255,255,255,.12);']),

                        Split::make([
                            TextEntry::make('reg_label')->label('')->state('Region')
                                ->weight('bold')->alignment('left'),
                            TextEntry::make('reg_value')->label('')
                                ->state(fn ($record) => $record->region?->name ?? '—'),
                        ])->columnSpan(4)->extraAttributes(['style' => 'gap:1px;padding:1px 0;border-bottom:1px solid rgba(255,255,255,.12);']),

                        Split::make([
                            TextEntry::make('appr_date_label')->label('')->state('Approval date')
                                ->weight('bold')->alignment('left'),
                            TextEntry::make('appr_date_value')->label('')
                                ->state(fn ($record) => $record->approval_status_updated_at?->format('Y-m-d') ?: '—'),
                        ])->columnSpan(4)->extraAttributes(['style' => 'gap:1px;padding:1px 0;border-bottom:1px solid rgba(255,255,255,.12);']),

                        /* Fila 5 */
                        Split::make([
                            TextEntry::make('purp_label')->label('')->state('Purpose')
                                ->weight('bold')->alignment('left'),
                            TextEntry::make('purp_value')->label('')
                                ->state(fn ($record) => $record->purpose ?: '—'),
                         ])->columnSpan(4)->extraAttributes(['style' => 'gap:1px;padding:1px 0;border-bottom:1px solid rgba(255,255,255,.12);']),

                        Split::make([
                            TextEntry::make('life_label')->label('')->state('Lifecycle status')
                                ->weight('bold')->alignment('left'),
                            TextEntry::make('life_value')->label('')
                                ->state(fn ($record) => $record->business_lifecycle_status ?: '—'),
                         ])->columnSpan(4)->extraAttributes(['style' => 'gap:1px;padding:1px 0;border-bottom:1px solid rgba(255,255,255,.12);']),

                        Split::make([
                            TextEntry::make('created_label')->label('')->state('Created at')
                                ->weight('bold')->alignment('left'),
                            TextEntry::make('created_value')->label('')
                                ->state(fn ($record) => $record->created_at?->format('Y-m-d H:i') ?: '—'),
                         ])->columnSpan(4)->extraAttributes(['style' => 'gap:1px;padding:1px 0;border-bottom:1px solid rgba(255,255,255,.12);']),
                    ]),
                ])
                ->maxWidth('7xl'),
                //->collapsible(),


            
        ]);
    }













// ╔═════════════════════════════════════════════════════════════════════════╗
// ║ Business Table                                                          ║
// ╚═════════════════════════════════════════════════════════════════════════╝

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('index')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('business_code')
                    ->searchable(),

                TextColumn::make('reinsurance_type')
                    ->searchable(),
                    
                TextColumn::make('reinsurer.short_name')
                    ->label('Reinsurer')
                    ->searchable(),

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

                TextColumn::make('renewed_from_id')
                    ->searchable(),

                TextColumn::make('currency.acronym')
                    ->label('Currency')
                    ->searchable(),

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
                //
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
                            ->leftJoin('partners', 'partners.id', '=', 'cost_nodesx.partner_id')

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
                    Tables\Actions\ViewAction::make()
                    ->label('View')
                    ->url(fn (Business $record) =>
                        self::getUrl('view', ['record' => $record])
                    )
                    ->icon('heroicon-m-eye'),  // opcional

                    // 👇 NUEVA OPCIÓN
                    Tables\Actions\Action::make('technical_result')
                    ->label('Technical result')
                    ->icon('heroicon-m-calculator')
                    ->action(fn () => Notification::make()
                        ->title('Technical result')
                        ->body('This feature is coming soon.')
                        ->info()
                        ->send()),

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
