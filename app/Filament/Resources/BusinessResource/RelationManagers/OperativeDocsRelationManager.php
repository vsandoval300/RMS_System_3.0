<?php

namespace App\Filament\Resources\BusinessResource\RelationManagers;

use App\Models\BusinessDocType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;           // 👈 importa la facade
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Filament\Forms\Components\FileUpload;
use Filament\Support\Enums\VerticalAlignment;
use Icetalker\FilamentTableRepeater\Forms\Components\TableRepeater;
use Filament\Support\RawJs;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use App\Models\CostScheme;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\View;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Actions;
use Filament\Forms\Get;
use Filament\Forms\Components\Actions\Action as FormAction;
use App\Models\CostScheme as CostSchemeModel;
use App\Models\Company;
use App\Models\Coverage;
use Carbon\Carbon;
use Filament\Support\Enums\MaxWidth;


use Nette\Utils\Html as UtilsHtml;

class OperativeDocsRelationManager extends RelationManager
{
    protected static string $relationship = 'OperativeDocs';
    protected static ?string $title = 'Operative Documents';
    protected static ?string $recordTitleAttribute = 'description';


    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with([
            'docType',
            'business',
            'schemes.costScheme.costNodexes', 
            'transactions',
        ]);
    }

    







    public function form(Form $form): Form
    {
        return $form->schema([


            Hidden::make('active_panel')
                ->default('tabs')   // 👈 por defecto Tabs abierto, Summary cerrado
                ->reactive()
                ->dehydrated(false) 
                ->afterStateHydrated(function (Forms\Set $set, $state) {
                        if (blank($state)) $set('active_panel', 'tabs');
                    }),

            /* ─────────────────────────  A) SECTION: TABS (colapsable)  ───────────────────────── */
            Section::make('Document Details')
                ->collapsible()
                ->collapsed(fn (Get $get) => $get('active_panel') !== 'tabs')
                ->extraAttributes([
                    'x-on:click.self' => '$wire.set("data.active_panel","tabs"); $wire.set("active_panel","tabs");',
                ])
                
                
                
                
                ->schema([




            Tabs::make('Operative Doc Form')
                ->columnSpanFull()
                ->tabs([


                    // 🟦 Primera burbuja: Insureds en otro Tab
                    // ╔═════════════════════════════════════════════════════════════════════════╗
                    // ║ Tab for Document Details                                                ║
                    // ╚═════════════════════════════════════════════════════════════════════════╝
                    Tab::make('Document Details')
                        ->icon('document')->icon('heroicon-o-document-text')
                        ->schema([

                            // 🟦 Primera burbuja: solo Id Document
                            Section::make()
                                ->schema([
                                    Grid::make(12)
                                        ->schema([
                                            Placeholder::make('')
                                                ->columnSpan(6), // deja media fila vacía

                                            TextInput::make('id')
                                                ->label('Id Document')
                                                ->disabled()
                                                ->dehydrated()
                                                ->required()
                                                ->columnSpan(6),
                                        ]),
                                ])
                                ->compact(),

                            // 🟦 Segunda burbuja: el resto de los campos
                            Section::make('Details')
                                ->schema([
                                    Textarea::make('description')
                                        ->label('Tittle')
                                        ->maxLength(255)
                                        ->columnSpanFull(),

                                    Select::make('operative_doc_type_id')
                                        ->label('Document Type')
                                        ->relationship('docType', 'name')
                                        ->required()
                                        ->live()
                                        ->preload(),

                                    /* Toggle::make('client_payment_tracking')
                                        ->label('Client Payment Tracking')
                                        ->default(false)
                                        ->helperText('Include tracking of payments from the original client if this option is enabled.'), */

                                    // 🟡 MODIFICACIÓN: inception_date ahora con afterStateUpdated
                                    DatePicker::make('inception_date')
                                        ->label('Inception Date')
                                        ->required()
                                        ->live() // 🟨 ✅ importante para reactividad EN VIVO
                                        ->afterStateUpdated(function (Forms\Set $set, $state, Forms\Get $get) {
                                            // opcional: puedes actualizar otro campo si quieres
                                        }),
                                        
                                    

                                    DatePicker::make('expiration_date')
                                        ->label('Expiration Date')
                                        ->required()
                                        ->live() // 🟨 ✅ importante para reactividad EN VIVO
                                        ->date()
                                        ->after('inception_date')
                                        ->validationMessages([
                                            'after' => 'The expiration date must be later than the inception date.',
                                            'required' => 'You must provide an expiration date.',
                                        ])
                                        ->afterStateUpdated(function (Forms\Set $set, $state, Forms\Get $get) {
                                            // opcional: puedes actualizar otro campo si quieres
                                        }),

                                     /* Hidden::make('roe')
                                        ->default(1), */
                                    

                                ])
                                ->columns(2)
                                ->compact(),

                                // 🟦 Tercera burbuja: solo el archivo
                                Section::make('File Upload')
                                    ->schema([
                                        FileUpload::make('document_path')
                                            ->label('File')
                                            ->disk('s3')
                                            ->directory('reinsurers/OperativeDocuments')
                                            ->visibility('private')
                                            ->acceptedFileTypes(['application/pdf'])
                                            ->preserveFilenames()
                                            ->downloadable()
                                            ->openable()
                                            ->previewable(true)
                                            ->hint(function ($record) {
                                                return $record?->document_path
                                                    ? 'Existing file: ' . basename($record->document_path)
                                                    : 'No file uploaded yet.';
                                            })
                                            ->dehydrated(fn ($state) => filled($state)) // <- solo guarda si hay nuevo valor
                                            ->helperText('Only PDF files are allowed.'),
                                    ])
                                    ->compact(),

                           /*  Section::make('File Upload')
                                ->schema([
                                    FileUpload::make('document_path')
                                        ->label('File')
                                        ->disk('s3')
                                        ->directory('reinsurers/OperativeDocuments')
                                        ->visibility('private')
                                        ->acceptedFileTypes(['application/pdf'])
                                        ->helperText('Only PDF files are allowed.'),
                                ])
                                ->compact(), */

                        ]),
                    
                    // 🟦 Segunda burbuja: Insureds en otro Tab
                    // ╔═════════════════════════════════════════════════════════════════════════╗
                    // ║ Tab for Placement Schemes                                               ║
                    // ╚═════════════════════════════════════════════════════════════════════════╝
                    Tab::make('Placement Schemes')
                        ->icon('heroicon-o-puzzle-piece')
                        ->schema([
                            Repeater::make('schemes')
                                ->label('Placement Schemes')
                                ->live()
                                ->relationship('schemes')
                                ->schema([
                                    Select::make('cscheme_id')
                                        ->label('Placement Scheme')
                                        ->options(
                                            \App\Models\CostScheme::all()->mapWithKeys(function ($scheme) {
                                                $shareFormatted = number_format($scheme->share * 100, 2) . '%';
                                                return [
                                                    $scheme->id => "{$scheme->id} · Index: {$scheme->index} · Share: {$shareFormatted} · Type: {$scheme->agreement_type}"
                                                ];
                                            })
                                        )

                                        ->searchable()
                                        ->preload()
                                        ->reactive()
                                        ->required(),

                                    Group::make()
                                        ->schema([
                                            View::make('partials.scheme-nodes-preview')
                                            ->viewData(fn ($get) => [
                                            'schemeId' => $get('cscheme_id'),
                                        ])
                                            ->columnSpan('full'),
                                        ]),
                                ])
                                ->columns(1)
                                ->defaultItems(0)
                                ->addActionLabel('Agregar esquema de colocación')
                                ->reorderable(false)
                                ->afterStateUpdated(function ($state, callable $set) {
                                    // 👇 Este callback permite que se refresque el resumen en vivo
                                    $set('schemes', $state);
                                }),

                    ]),




                    // 🟦 Tercera burbuja: Insureds en otro Tab
                    // ╔═════════════════════════════════════════════════════════════════════════╗
                    // ║ Tab for Insured Members.                                                ║
                    // ╚═════════════════════════════════════════════════════════════════════════╝
                    Tab::make('Insured Members')
                        ->icon('heroicon-o-users')
                        ->schema([
                            Grid::make(12)
                                ->schema([

                                    Repeater::make('insureds')
                                        ->label('Insureds')
                                        ->relationship()
                                        ->schema([
                                            Select::make('company_id')
                                                ->label('Company')
                                                ->relationship('company', 'name')
                                                ->preload()
                                                ->required()
                                                ->searchable()
                                                ->columnSpan(5),

                                            Select::make('coverage_id')
                                                ->label('Coverage')
                                                ->relationship('coverage', 'name')
                                                ->preload()
                                                ->required()
                                                ->searchable()
                                                ->columnSpan(4),

                                            TextInput::make('premium')
                                                ->label('Premium')
                                                ->prefix('$')
                                                ->type('text')                 // evita <input type="number">
                                                ->inputMode('decimal')         // teclado numérico en móvil
                                                ->live(onBlur: true)           // o ->live(debounce: 500)
                                                ->mask(RawJs::make('$money($input)'))   // solo para visual
                                                // NO usar ->stripCharacters(',') aquí
                                                // NO usar ->numeric() aquí
                                                ->dehydrateStateUsing(fn ($state) => $state !== null
                                                    ? (float) str_replace([',', '$', ' '], '', $state)
                                                    : null
                                                )
                                                ->step(0.01)
                                                ->required()
                                                ->columnSpan(3),

                                            /* TextInput::make('premium')
                                                ->prefix('$')
                                                ->required()
                                                ->live()
                                                ->mask(RawJs::make('$money($input)'))
                                                ->stripCharacters(',') 
                                                ->dehydrateStateUsing(fn ($state) => $state !== null ? floatval(str_replace(',', '', $state)) : null)                                            
                                                ->numeric()
                                                //->dehydrateStateUsing(fn ($state) => floatval(preg_replace('/[^0-9.]/', '', $state)))
                                                ->step(0.01)
                                               ->columnSpan(3), */
                                        ])
                                        ->reorderableWithButtons()
                                        ->defaultItems(1)
                                        ->columns(12)
                                        ->addActionLabel('Add Insured')
                                        //->reorderable(false)
                                        ->columnSpan(12) // 👈 fuerza a ocupar todo el ancho
                                        ->live()
                                        ->afterStateUpdated(function ($state, callable $set) {
                                            $total = collect($state)
                                                ->pluck('premium')
                                                ->filter()
                                                ->map(fn ($value) => floatval(str_replace(',', '', $value)))
                                                ->sum();

                                            $set('insureds_total', number_format($total, 2, '.', ','));
                                        }),

                                    Placeholder::make('')->columnSpan(9),

                                    TextInput::make('insureds_total')
                                        ->label('Grand Total Premium')
                                        ->prefix('$')
                                        ->disabled()
                                        ->dehydrated(false)
                                        ->columnSpan(3), // 👈 mismo span para alinear con el repeater
                                ]),
                        ]),
                    


                   
                    // 🟦 Cuarta burbuja: Insureds en otro Tab
                    // ╔═════════════════════════════════════════════════════════════════════════╗
                    // ║ Tab for Installments.                                                   ║
                    // ╚═════════════════════════════════════════════════════════════════════════╝                                                 
                   
                    Tab::make('Installments')
                        ->icon('heroicon-o-banknotes')
                        ->reactive()
                        ->live()
                        ->schema([
                            Repeater::make('transactions')
                                ->label('Installments')
                                ->relationship()
                                ->schema([
                                   TextInput::make('index')
                                        ->label('Index')
                                        ->disabled()
                                        ->dehydrated()
                                        ->required()
                                        ->live()
                                        ->numeric()
                                        ->columnSpan(1),
                    
                                    TextInput::make('proportion')
                                        ->label('Proportion')
                                        ->suffix('%')
                                        ->required()
                                         ->live()
                                        ->minValue(0)
                                        ->maxValue(100)
                                        ->step(0.01)
                                        ->mask(RawJs::make('$money($input, ".", ",", 2)')) // se ve como 70.00
                                        ->reactive()
                                        ->formatStateUsing(fn ($state) => $state !== null ? round($state * 100, 2) : null) // decimal → porcentaje
                                        ->dehydrateStateUsing(fn ($state) => floatval(str_replace(',', '', $state)) / 100) // porcentaje → decimal
                                        ->columnSpan(1),

                                   TextInput::make('exch_rate')
                                        ->label('Exchange Rate')
                                        ->numeric()
                                        ->required()
                                         ->live()
                                        ->step(0.00001)
                                        ->columnSpan(1),

                                    DatePicker::make('due_date')
                                        ->label('Due Date')
                                        ->required()
                                        ->live()
                                        ->columnSpan(1),

                                    // Campos ocultos: se asignan automáticamente
                                    Hidden::make('remmitance_code')->default(null),
                                    Hidden::make('transaction_type_id')->default(1),
                                    Hidden::make('transaction_status_id')->default(1),
                                    Hidden::make('op_document_id')->default(fn () => $this->getOwnerRecord()?->id),
                                ])
                                ->reorderableWithButtons()
                                ->defaultItems(0)
                                ->columns(4)
                                ->addActionLabel('New Installment')
                                ->afterStateUpdated(function (array $state, callable $set) {
                                    $newState = [];
                                    $index = 1;

                                    foreach ($state as $key => $item) {
                                        if (is_array($item)) {
                                            $item['index'] = $index;
                                            $newState[$key] = $item;
                                            $index++;
                                        }
                                    }

                                    $set('transactions', $newState);
                                })


                    ]),
            ])
            ->columnSpanFull(),







                                // 🔐 Validación personalizada para 100%
                                /* ->rules([
                                    function (\Filament\Forms\Get $get) {
                                        return function (string $attribute, $value, \Closure $fail) use ($get) {
                                            $total = collect($get('transactions'))
                                                ->pluck('proportion')
                                                ->filter()
                                                ->map(fn ($v) => floatval(str_replace(',', '', $v)) * 100) // decimal → porcentaje
                                                ->sum();

                                            if (round($total, 2) !== 100.0) {
                                                $fail('La suma de las proporciones debe ser exactamente 100 %.');
                                            }
                                        };
                                    }
                                ]), */
                        ]),


                    /* Tab::make('Calculations')
                        ->schema([
                            View::make('filament.resources.business.summary-html')
                                ->reactive()
                                ->viewData(fn ($get, $record) => [
                                    'id' => $get('id'),
                                    'createdAt' => optional($record)->created_at,
                                    'documentType' => $record && $record->docType ? $record->docType->name : '-',
                                    'inceptionDate' => $get('inception_date'),
                                    'expirationDate' => $get('expiration_date'),
                                    'premiumType' => $record && $record->business ? $record->business->premium_type : '-',

                                    // ✅ Evitamos errores si $record es null
                                    'insureds' => $record?->insureds()?->with(['company.country', 'coverage', 'transactions'])->get() ?? collect(),
                                    'totalPremium' => $record?->insureds()?->sum('premium') ?? 0,
                                    'costSchemes' => $record?->schemes()?->with('costScheme')->get() ?? collect(),

                                    // ✅ Evitamos error en cascada
                                    'costNodes' => $record?->schemes()
                                        ?->with('costScheme.costNodexes.partner', 'costScheme.costNodexes.deduction')
                                        ?->get()
                                        ?->pluck('costScheme')
                                        ?->flatMap(fn ($cs) => $cs->costNodexes) ?? collect(),

                                    // ✅ sigue siendo útil tener el record
                                    'record' => $record,

                                ]),
                            ]), 
                ]),*/
            

            /* // 🔵 Button that opens modal with the Overview Blade
            Actions::make([
                FormAction::make('show_overview')
                    ->label('Overview')
                    ->icon('heroicon-o-rectangle-group')
                    ->color('gray')
                    ->modalHeading('Overview')
                    ->modalWidth(MaxWidth::SevenExtraLarge) // '7xl'
                    ->modalSubmitAction(false)              // sin botón de submit
                    ->modalCancelActionLabel('Close')       // solo botón Close
                    ->action(fn () => null)                 // no-op
                    ->modalContent(function (Get $get, $record, $livewire) {
                        // Reusa la misma data que calculabas en viewData:
                        $data = $this->buildOverviewData($get, $record, $livewire);
                        return view('filament.resources.business.operative-doc-summary', $data);
                    }),
            ])
                ->alignment('left')
                ->columnSpanFull(), */
 






                

            // 🟡 SPACE 
            //-------------------------------------------------------
            Placeholder::make('')
                ->content('')
                ->columnSpanFull()
                ->extraAttributes(['class' => 'my-1']), // 👈 margen vertical
            
            
           
            

           
           
           
           
            //-------------------------------------------------------
            // 🟡 SUMMARY Section
            //-------------------------------------------------------
            Section::make('Overview')
                ->collapsible()
                ->collapsed(fn (Get $get) => $get('active_panel') !== 'summary')
                //->extraAttributes([
                //    'class' => 'max-h-[550px] overflow-y-auto'
                //]),
                ->extraAttributes([
                    'x-on:click.self' => '$wire.set("data.active_panel","summary"); $wire.set("active_panel","summary");',
                    'class' => 'max-h-[700px] overflow-y-auto',
                ])





                ->schema([
                    View::make('filament.resources.business.operative-doc-summary')
                        ->extraAttributes([
                            'class' => 'bg-[#dfe0e2] text-black p-4 rounded-md'
                        ])
                        ->reactive()
                        ->reactive()
                        ->viewData(function ($get, $record, $livewire) {
                            $business = method_exists($livewire, 'getOwnerRecord') ? $livewire->getOwnerRecord() : null;

                            // 🔸 Schemes con datos relevantes ya cargados
                            $schemes = collect($get('schemes') ?? [])
                                ->map(function ($scheme) {
                                    $model = \App\Models\CostScheme::find($scheme['cscheme_id'] ?? null);
                                    return $model ? [
                                        'id' => $model->id,
                                        'share' => $model->share,
                                        'agreement_type' => $model->agreement_type,
                                    ] : null;
                                })
                                ->filter()
                                ->values()
                                ->toArray();
                            
                            $totalShare = collect($schemes)->sum('share'); // 🔹 total calculado


                            // 🔹 Insureds con limpieza de premium
                            $insureds = collect($get('insureds') ?? [])->map(function ($insured) {
                                $company = \App\Models\Company::with('country')->find($insured['company_id'] ?? null);
                                $coverage = \App\Models\Coverage::find($insured['coverage_id'] ?? null);

                                $raw = $insured['premium'] ?? 0;
                                $clean = is_string($raw) ? preg_replace('/[^0-9.]/', '', $raw) : $raw;
                                if (is_string($clean)) {
                                    $parts = explode('.', $clean, 3);
                                    $clean = isset($parts[1]) ? $parts[0] . '.' . $parts[1] : $parts[0];
                                }
                                $premium = floatval($clean);

                                return [
                                    'company' => $company
                                        ? [
                                            'name' => $company->name,
                                            'country' => ['name' => optional($company->country)->name],
                                        ]
                                        : ['name' => '-', 'country' => ['name' => '-']],
                                    'coverage' => $coverage
                                        ? ['name' => $coverage->name]
                                        : ['name' => '-'],
                                    'premium' => $premium,
                                ];
                            })->toArray();

                            $costNodes = collect($get('schemes') ?? [])
                                ->map(fn ($scheme) => \App\Models\CostScheme::with('costNodexes.costScheme', 'costNodexes.partner', 'costNodexes.deduction')
                                    ->find($scheme['cscheme_id'] ?? null))
                                ->filter()
                                ->flatMap(fn ($scheme) => $scheme->costNodexes ?? collect())
                                ->values();

                            // 📊 Cálculos generales
                            $inception = $get('inception_date');
                            $expiration = $get('expiration_date');
                            $start = $inception ? \Carbon\Carbon::parse($inception) : null;
                            $end = $expiration ? \Carbon\Carbon::parse($expiration) : null;
                            $coverageDays = ($start && $end) ? $start->diffInDays($end) : 0;
                            $daysInYear = $start && $start->isLeapYear() ? 366 : 365;

                            $totalPremium = collect($insureds)->sum('premium');
                            $insureds = collect($insureds)->map(function ($insured) use ($totalPremium, $coverageDays, $daysInYear, $schemes) {
                                $allocation = $totalPremium > 0 ? $insured['premium'] / $totalPremium : 0;
                                $premiumFtp = ($daysInYear > 0) ? ($insured['premium'] / $daysInYear) * $coverageDays : 0;

                                // Aplica todos los shares al insured individual para calcular su FTS
                                $premiumFts = 0;
                                foreach ($schemes as $s) {
                                    $premiumFts += $premiumFtp * ($s['share'] ?? 0);
                                }

                                return array_merge($insured, [
                                    'allocation_percent' => $allocation,
                                    'premium_ftp' => $premiumFtp,
                                    'premium_fts' => $premiumFts,
                                ]);
                            })->toArray();
                            
                            
                            $totalPremiumFtp = ($daysInYear > 0) ? ($totalPremium / $daysInYear) * $coverageDays : 0;

                            
                            
                            $totalPremiumFts = 0;
                            foreach ($schemes as $s) {
                                $totalPremiumFts += $totalPremiumFtp * ($s['share'] ?? 0);
                            }



                            //Converted Premium Formula
                            // -Transform Annual Premium Fts according their installments parameters//
                            $transactions = collect($get('transactions') ?? []);
                            $totalConvertedPremium = 0;

                            foreach ($transactions as $txn) {
                                $proportion = floatval($txn['proportion'] ?? 0) / 100; // 👈 CORRECTO
                                $rate = floatval($txn['exch_rate'] ?? 0);
                                if ($rate > 0) {
                                    $totalConvertedPremium += ($totalPremiumFts * $proportion) / $rate;
                                } else {
                                    $totalConvertedPremium = 1;
                                }
                            }

                            $totalDeductionOrig = 0;
                            $totalDeductionUsd = 0;

                            $groupedCostNodes = $costNodes->groupBy(fn ($node) => $node->costSchemes->share ?? 0)
                                ->map(function ($nodes, $share) use (&$totalDeductionOrig, &$totalDeductionUsd, $totalPremiumFts, $totalConvertedPremium) {
                                    $shareFloat = floatval($share);

                                    $nodeList = $nodes->map(function ($node) use ($shareFloat, $totalPremiumFts, $totalConvertedPremium) {
                                        $deduction = $totalPremiumFts * $node->value * $shareFloat;
                                        $deductionConverted = $totalConvertedPremium * $node->value * $shareFloat;

                                        return [
                                            'index' => $node->index,
                                            'partner' => $node->partner?->name ?? '-',
                                            'deduction' => $node->deduction?->concept ?? '-',
                                            'value' => $node->value,
                                            'share' => $shareFloat,
                                            'deduction_amount' => $deduction,
                                            'deduction_usd' => $deductionConverted,
                                        ];
                                    })->values();

                                    $subtotalOrig = $nodeList->sum('deduction_amount');
                                    $subtotalUsd = $nodeList->sum('deduction_usd');

                                    $totalDeductionOrig += $subtotalOrig;
                                    $totalDeductionUsd += $subtotalUsd;

                                    return [
                                        'share' => $shareFloat,
                                        'nodes' => $nodeList,
                                        'subtotal_orig' => $subtotalOrig,
                                        'subtotal_usd' => $subtotalUsd,
                                    ];
                                })
                                ->sortKeys()
                                ->values()
                                ->toArray();




                            return [
                                'id' => $get('id'),
                                'createdAt' => $record?->created_at ?? now(),
                                'documentType' => ($docTypeId = $get('operative_doc_type_id'))
                                    ? \App\Models\BusinessDocType::find($docTypeId)?->name ?? '-'
                                    : '-',
                                'inceptionDate' => $inception,
                                'expirationDate' => $expiration,
                                'premiumType' => $record?->business?->premium_type
                                    ?? $business?->premium_type
                                    ?? '-',
                                'originalCurrency' => $record?->business?->currency?->acronym
                                    ?? $business?->currency?->acronym
                                    ?? '-',
                                'insureds' => array_values($insureds),
                                'costSchemes' => $schemes,
                                'groupedCostNodes' => $groupedCostNodes,
                                'totalPremiumFts' => $totalPremiumFts,
                                'totalPremiumFtp' => $totalPremiumFtp,
                                'totalConvertedPremium' => $totalConvertedPremium,
                                'coverageDays' => $coverageDays,
                                'totalDeductionOrig' => $totalDeductionOrig,
                                'totalDeductionUsd' => $totalDeductionUsd,
                                'totalShare' => $totalShare,
                                'transactions' => collect($get('transactions') ?? [])->values(),
                            ];
                        }),
                ])
                ->columnSpanFull(),
                
                
                


                

        ]);
    }


//-------------------------------------------------------
            // 🟡 BUTTON Section
            //-

/* protected function buildOverviewData(\Filament\Forms\Get $get, $record, $livewire): array
{
    $business = method_exists($livewire, 'getOwnerRecord') ? $livewire->getOwnerRecord() : null;

    // 🔸 Schemes con datos relevantes ya cargados
    $schemes = collect($get('schemes') ?? [])
        ->map(function ($scheme) {
            $model = \App\Models\CostScheme::find($scheme['cscheme_id'] ?? null);
            return $model ? [
                'id' => $model->id,
                'share' => $model->share,
                'agreement_type' => $model->agreement_type,
            ] : null;
        })
        ->filter()
        ->values()
        ->toArray();

    $totalShare = collect($schemes)->sum('share'); // 🔹 total calculado

    // 🔹 Insureds con limpieza de premium
    $insureds = collect($get('insureds') ?? [])->map(function ($insured) {
        $company  = \App\Models\Company::with('country')->find($insured['company_id'] ?? null);
        $coverage = \App\Models\Coverage::find($insured['coverage_id'] ?? null);

        $raw   = $insured['premium'] ?? 0;
        $clean = is_string($raw) ? preg_replace('/[^0-9.]/', '', $raw) : $raw;
        if (is_string($clean)) {
            $parts = explode('.', $clean, 3);
            $clean = isset($parts[1]) ? $parts[0] . '.' . $parts[1] : $parts[0];
        }
        $premium = floatval($clean);

        return [
            'company' => $company
                ? [
                    'name'    => $company->name,
                    'country' => ['name' => optional($company->country)->name],
                ]
                : ['name' => '-', 'country' => ['name' => '-']],
            'coverage' => $coverage ? ['name' => $coverage->name] : ['name' => '-'],
            'premium'  => $premium,
        ];
    })->toArray();

    // 🔹 Cost nodes de los schemes elegidos
    $costNodes = collect($get('schemes') ?? [])
        ->map(fn ($scheme) => \App\Models\CostScheme::with('costNodexes.costScheme', 'costNodexes.partner', 'costNodexes.deduction')
            ->find($scheme['cscheme_id'] ?? null))
        ->filter()
        ->flatMap(fn ($scheme) => $scheme->costNodexes ?? collect())
        ->values();

    // 📊 Cálculos generales de fechas y prorrateos
    $inception  = $get('inception_date');
    $expiration = $get('expiration_date');
    $start = $inception  ? \Carbon\Carbon::parse($inception)  : null;
    $end   = $expiration ? \Carbon\Carbon::parse($expiration) : null;
    $coverageDays = ($start && $end) ? $start->diffInDays($end) : 0;
    $daysInYear   = $start && $start->isLeapYear() ? 366 : 365;

    $totalPremium = collect($insureds)->sum('premium');

    $insureds = collect($insureds)->map(function ($insured) use ($totalPremium, $coverageDays, $daysInYear, $schemes) {
        $allocation  = $totalPremium > 0 ? $insured['premium'] / $totalPremium : 0;
        $premiumFtp  = ($daysInYear > 0) ? ($insured['premium'] / $daysInYear) * $coverageDays : 0;

        // Aplica todos los shares al insured individual para calcular su FTS
        $premiumFts = 0;
        foreach ($schemes as $s) {
            $premiumFts += $premiumFtp * ($s['share'] ?? 0);
        }

        return array_merge($insured, [
            'allocation_percent' => $allocation,
            'premium_ftp'        => $premiumFtp,
            'premium_fts'        => $premiumFts,
        ]);
    })->toArray();

    $totalPremiumFtp = ($daysInYear > 0) ? ($totalPremium / $daysInYear) * $coverageDays : 0;

    $totalPremiumFts = 0;
    foreach ($schemes as $s) {
        $totalPremiumFts += $totalPremiumFtp * ($s['share'] ?? 0);
    }

    // Converted Premium (por installments)
    $transactions = collect($get('transactions') ?? []);
    $totalConvertedPremium = 0;
    foreach ($transactions as $txn) {
        $proportion = floatval($txn['proportion'] ?? 0) / 100; // porcentual → decimal
        $rate       = floatval($txn['exch_rate'] ?? 0);
        if ($rate > 0) {
            $totalConvertedPremium += ($totalPremiumFts * $proportion) / $rate;
        } else {
            // si no hay tipo de cambio válido, evita división entre 0
            $totalConvertedPremium = 1;
        }
    }

    // Agrupación y subtotales por share (de los cost nodes)
    $totalDeductionOrig = 0;
    $totalDeductionUsd  = 0;

    $groupedCostNodes = $costNodes->groupBy(fn ($node) => $node->costSchemes->share ?? 0)
        ->map(function ($nodes, $share) use (&$totalDeductionOrig, &$totalDeductionUsd, $totalPremiumFts, $totalConvertedPremium) {
            $shareFloat = floatval($share);

            $nodeList = $nodes->map(function ($node) use ($shareFloat, $totalPremiumFts, $totalConvertedPremium) {
                $deduction          = $totalPremiumFts       * ($node->value ?? 0) * $shareFloat;
                $deductionConverted = $totalConvertedPremium * ($node->value ?? 0) * $shareFloat;

                return [
                    'index'             => $node->index,
                    'partner'           => $node->partner?->name ?? '-',
                    'deduction'         => $node->deduction?->concept ?? '-',
                    'value'             => $node->value,
                    'share'             => $shareFloat,
                    'deduction_amount'  => $deduction,
                    'deduction_usd'     => $deductionConverted,
                ];
            })->values();

            $subtotalOrig = $nodeList->sum('deduction_amount');
            $subtotalUsd  = $nodeList->sum('deduction_usd');

            $totalDeductionOrig += $subtotalOrig;
            $totalDeductionUsd  += $subtotalUsd;

            return [
                'share'         => $shareFloat,
                'nodes'         => $nodeList,
                'subtotal_orig' => $subtotalOrig,
                'subtotal_usd'  => $subtotalUsd,
            ];
        })
        ->sortKeys()
        ->values()
        ->toArray();

    // 🔚 Payload para la vista del reporte
    return [
        'id'                   => $get('id'),
        'createdAt'            => $record?->created_at ?? now(),
        'documentType'         => ($docTypeId = $get('operative_doc_type_id'))
            ? \App\Models\BusinessDocType::find($docTypeId)?->name ?? '-'
            : '-',
        'inceptionDate'        => $inception,
        'expirationDate'       => $expiration,
        'premiumType'          => $record?->business?->premium_type
            ?? $business?->premium_type
            ?? '-',
        'originalCurrency'     => $record?->business?->currency?->acronym
            ?? $business?->currency?->acronym
            ?? '-',
        'insureds'             => array_values($insureds),
        'costSchemes'          => $schemes,
        'groupedCostNodes'     => $groupedCostNodes,
        'totalPremiumFts'      => $totalPremiumFts,
        'totalPremiumFtp'      => $totalPremiumFtp,
        'totalConvertedPremium'=> $totalConvertedPremium,
        'coverageDays'         => $coverageDays,
        'totalDeductionOrig'   => $totalDeductionOrig,
        'totalDeductionUsd'    => $totalDeductionUsd,
        'totalShare'           => $totalShare,
        'transactions'         => collect($get('transactions') ?? [])->values(),
    ];
} */









//NEW Button para renderizar el informe

/* protected function buildOverviewData(Get $get, $record, $livewire): array
{
    $business = method_exists($livewire, 'getOwnerRecord') ? $livewire->getOwnerRecord() : null;

    // 🔸 Schemes with relevant data
    $schemes = collect($get('schemes') ?? [])
        ->map(function ($scheme) {
            $model = CostSchemeModel::find($scheme['cscheme_id'] ?? null);
            return $model ? [
                'id' => $model->id,
                'share' => $model->share,
                'agreement_type' => $model->agreement_type,
            ] : null;
        })
        ->filter()
        ->values()
        ->toArray();

    $totalShare = collect($schemes)->sum('share');

    // 🔹 Insureds with cleaned premium
    $insureds = collect($get('insureds') ?? [])->map(function ($insured) {
        $company = Company::with('country')->find($insured['company_id'] ?? null);
        $coverage = Coverage::find($insured['coverage_id'] ?? null);

        $raw = $insured['premium'] ?? 0;
        $clean = is_string($raw) ? preg_replace('/[^0-9.]/', '', $raw) : $raw;
        if (is_string($clean)) {
            $parts = explode('.', $clean, 3);
            $clean = isset($parts[1]) ? $parts[0] . '.' . $parts[1] : $parts[0];
        }
        $premium = floatval($clean);

        return [
            'company' => $company
                ? [
                    'name' => $company->name,
                    'country' => ['name' => optional($company->country)->name],
                ]
                : ['name' => '-', 'country' => ['name' => '-']],
            'coverage' => $coverage
                ? ['name' => $coverage->name]
                : ['name' => '-'],
            'premium' => $premium,
        ];
    })->toArray();

    $costNodes = collect($get('schemes') ?? [])
        ->map(fn ($scheme) => \App\Models\CostScheme::with('costNodexes.costScheme', 'costNodexes.partner', 'costNodexes.deduction')
            ->find($scheme['cscheme_id'] ?? null))
        ->filter()
        ->flatMap(fn ($scheme) => $scheme->costNodexes ?? collect())
        ->values();

    // 📊 General calculations
    $inception = $get('inception_date');
    $expiration = $get('expiration_date');
    $start = $inception ? Carbon::parse($inception) : null;
    $end = $expiration ? Carbon::parse($expiration) : null;
    $coverageDays = ($start && $end) ? $start->diffInDays($end) : 0;
    $daysInYear = $start && $start->isLeapYear() ? 366 : 365;

    $totalPremium = collect($insureds)->sum('premium');
    $insureds = collect($insureds)->map(function ($insured) use ($totalPremium, $coverageDays, $daysInYear, $schemes) {
        $allocation = $totalPremium > 0 ? $insured['premium'] / $totalPremium : 0;
        $premiumFtp = ($daysInYear > 0) ? ($insured['premium'] / $daysInYear) * $coverageDays : 0;

        $premiumFts = 0;
        foreach ($schemes as $s) {
            $premiumFts += $premiumFtp * ($s['share'] ?? 0);
        }

        return array_merge($insured, [
            'allocation_percent' => $allocation,
            'premium_ftp' => $premiumFtp,
            'premium_fts' => $premiumFts,
        ]);
    })->toArray();

    $totalPremiumFtp = ($daysInYear > 0) ? ($totalPremium / $daysInYear) * $coverageDays : 0;

    $totalPremiumFts = 0;
    foreach ($schemes as $s) {
        $totalPremiumFts += $totalPremiumFtp * ($s['share'] ?? 0);
    }

    // Converted Premium Formula (per installments)
    $transactions = collect($get('transactions') ?? []);
    $totalConvertedPremium = 0;
    foreach ($transactions as $txn) {
        $proportion = floatval($txn['proportion'] ?? 0) / 100; // porcentual → decimal
        $rate = floatval($txn['exch_rate'] ?? 0);
        $totalConvertedPremium += ($totalPremiumFts * $proportion) / $rate;
    }

    $totalDeductionOrig = 0;
    $totalDeductionUsd = 0;

    $groupedCostNodes = $costNodes->groupBy(fn ($node) => $node->costSchemes->share ?? 0)
        ->map(function ($nodes, $share) use (&$totalDeductionOrig, &$totalDeductionUsd, $totalPremiumFts, $totalConvertedPremium) {
            $shareFloat = floatval($share);

            $nodeList = $nodes->map(function ($node) use ($shareFloat, $totalPremiumFts, $totalConvertedPremium) {
                $deduction = $totalPremiumFts * $node->value * $shareFloat;
                $deductionConverted = $totalConvertedPremium * $node->value * $shareFloat;

                return [
                    'index' => $node->index,
                    'partner' => $node->partner?->name ?? '-',
                    'deduction' => $node->deduction?->concept ?? '-',
                    'value' => $node->value,
                    'share' => $shareFloat,
                    'deduction_amount' => $deduction,
                    'deduction_usd' => $deductionConverted,
                ];
            })->values();

            $subtotalOrig = $nodeList->sum('deduction_amount');
            $subtotalUsd = $nodeList->sum('deduction_usd');

            $totalDeductionOrig += $subtotalOrig;
            $totalDeductionUsd += $subtotalUsd;

            return [
                'share' => $shareFloat,
                'nodes' => $nodeList,
                'subtotal_orig' => $subtotalOrig,
                'subtotal_usd' => $subtotalUsd,
            ];
        })
        ->sortKeys()
        ->values()
        ->toArray();

    return [
        'id' => $get('id'),
        'createdAt' => $record?->created_at ?? now(),
        'documentType' => ($docTypeId = $get('operative_doc_type_id'))
            ? BusinessDocType::find($docTypeId)?->name ?? '-'
            : '-',
        'inceptionDate' => $inception,
        'expirationDate' => $expiration,
        'premiumType' => $record?->business?->premium_type
            ?? $business?->premium_type
            ?? '-',
        'originalCurrency' => $record?->business?->currency?->acronym
            ?? $business?->currency?->acronym
            ?? '-',
        'insureds' => array_values($insureds),
        'costSchemes' => $schemes,
        'groupedCostNodes' => $groupedCostNodes,
        'totalPremiumFts' => $totalPremiumFts,
        'totalPremiumFtp' => $totalPremiumFtp,
        'totalConvertedPremium' => $totalConvertedPremium,
        'coverageDays' => $coverageDays,
        'totalDeductionOrig' => $totalDeductionOrig,
        'totalDeductionUsd' => $totalDeductionUsd,
        'totalShare' => $totalShare,
        'transactions' => collect($get('transactions') ?? [])->values(),
    ];
} */
//NEW










    public function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('index')
                ->sortable()->verticalAlignment(VerticalAlignment::Start) 
                ->sortable()
                ->searchable(),

            Tables\Columns\TextColumn::make('id')
                ->label('Document code')
                ->sortable()->verticalAlignment(VerticalAlignment::Start) 
                ->copyable()
                ->sortable()
                ->searchable()
                ->tooltip(fn ($state) => $state) 
                ->extraAttributes(['class' => 'w-64']), // 👈 Ajusta el ancho

            Tables\Columns\TextColumn::make('docType.name')
                ->label('Doc Type')
                ->sortable()->verticalAlignment(VerticalAlignment::Start) 
                ->sortable()
                ->searchable(),

            Tables\Columns\TextColumn::make('description')
                ->searchable()
                ->sortable()->verticalAlignment(VerticalAlignment::Start) 
                ->wrap() // 👈 permite múltiples líneas
                ->extraAttributes([
                        'style' => 'width: 250px; white-space: normal; vertical-align: top;',
                    ]),

            Tables\Columns\TextColumn::make('inception_date')
                ->sortable()->verticalAlignment(VerticalAlignment::Start)   
                ->date(),

            Tables\Columns\TextColumn::make('expiration_date')
                ->sortable()->verticalAlignment(VerticalAlignment::Start) 
                ->date(),
            
            Tables\Columns\TextColumn::make('status')
                ->label('Status')
                ->sortable()->verticalAlignment(VerticalAlignment::Start) 
                ->badge()
                ->state(fn ($record) => match (true) {
                    now()->lt($record->inception_date)           => 'Pending',
                    now()->lte($record->expiration_date)         => 'In Force',
                    default                                      => 'Expired',
                })
                ->color(fn (string $state): string => match ($state) {
                    'Pending'  => 'gray',
                    'In Force' => 'success',
                    'Expired'  => 'danger',
                }),


            Tables\Columns\IconColumn::make('document_path')
                ->label('File')                         // sin encabezado
                ->sortable()->verticalAlignment(VerticalAlignment::Start) 
                ->getStateUsing(fn ($record) => true) // ← fuerza que siempre se pinte
                ->icon(fn ($record) =>
                        $record->document_path ? 'heroicon-o-document' : 'heroicon-o-x-circle'
                    )



                ->color(fn ($record) => $record->document_path ? 'primary' : 'danger')
                ->url(function ($record) {
                    if (! $record->document_path) {
                        return null; // 👈 evita error si es null
                    }

                    /** @var \Illuminate\Filesystem\FilesystemAdapter $s3 */
                    $s3 = Storage::disk('s3');

                    return Str::startsWith(
                        $record->document_path,
                        ['http://', 'https://']
                    )
                        ? $record->document_path
                        : $s3->url($record->document_path);
                    
                })
                ->openUrlInNewTab()
                ->tooltip(fn ($record) =>
                    $record->document_path ? 'View PDF' : 'No document available'
                ),

        ])


        ->filters([
            //
        ])
        ->headerActions([
            Tables\Actions\CreateAction::make()
                ->label('Create Operative Doc')
               ->beforeFormFilled(function ($livewire, $action) {
                    $business = $livewire->ownerRecord;

                    // Obtener el sufijo numérico más alto en IDs anteriores (incluyendo eliminados)
                    $lastIndex = $business->operativeDocs()
                        ->withTrashed()
                        ->get()
                        ->map(function ($doc) {
                            // Extrae los últimos 2 o 3 dígitos del ID
                            return intval(substr($doc->id, -2));
                        })
                        ->max();

                    $newIndex = $lastIndex ? $lastIndex + 1 : 1;
                    $generatedId = $business->business_code . '-' . str_pad($newIndex, 2, '0', STR_PAD_LEFT);

                    $action->fillForm([
                        'id' => $generatedId,
                    ]);


                })
                ->mutateFormDataUsing(function (array $data, $livewire) {
                    if (! isset($data['id'])) {
                        $business = $livewire->ownerRecord;

                        $lastIndex = $business->operativeDocs()
                            ->withTrashed()
                            ->get()
                            ->map(function ($doc) {
                                return intval(substr($doc->id, -2));
                            })
                            ->max();

                        $newIndex = $lastIndex ? $lastIndex + 1 : 1;
                        $data['id'] = $business->business_code . '-' . str_pad($newIndex, 2, '0', STR_PAD_LEFT);
                    }

                    return $data;
                })
                //->formModalWidth('xl') // opcional: para mayor espacio
        ])
        ->actions([
            Tables\Actions\ActionGroup::make([
                Tables\Actions\ViewAction::make('view')
                    ->label('View')
                    ->modalHeading(fn ($record) => '📄 Reviewing ' . $record->docType->name .' — '. $record->id )
                    ->modalWidth('6xl'),  

                Tables\Actions\EditAction::make('edit')
                    ->label('Edit')
                    ->modalHeading(fn ($record) => '📝 Modifying ' . $record->docType->name .' — '. $record->id )
                    ->modalWidth('6xl'), 

                Tables\Actions\DeleteAction::make(),
            ]),
        ])
        ->bulkActions([
            //Tables\Actions\DeleteBulkAction::make(),
        ]);
    }
}
