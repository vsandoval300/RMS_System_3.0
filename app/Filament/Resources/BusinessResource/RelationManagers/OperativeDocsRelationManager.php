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
use Filament\Forms\Set;
use App\Models\CostScheme as CostSchemeModel;
use App\Models\Company;
use App\Models\Coverage;
use Carbon\Carbon;
use Filament\Support\Enums\MaxWidth;
use Filament\Notifications\Notification;
use App\Filament\Resources\TransactionLogResource;
use Filament\Forms\Components\Actions\Action as FormAction;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Actions\ActionGroup;
use Illuminate\Support\Facades\DB;
use App\Services\TransactionLogBuilder;
use Filament\Forms\Components\Fieldset;
use Illuminate\Support\HtmlString;
use Illuminate\Validation\ValidationException;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;





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

    

    // ──────FORM CREATE / EDIT  ─────────────────────────
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

                // ────────  A) SECTION: TABS (colapsable)  ─────────────────────────
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
                                //----------------------------------------------------  
                                //🔵 1.-Tab for Document Details 
                                //----------------------------------------------------
                                Tab::make('Document Details')
                                    ->icon('document')->icon('heroicon-o-document-text')
                                    ->schema([

                                        //Primera burbuja: solo Id Document
                                        Section::make()
                                            ->schema([
                                                Grid::make(12)
                                                    ->schema([
                                                        Placeholder::make('')
                                                            ->columnSpan(12), // deja media fila vacía

                                                        TextInput::make('id')
                                                            ->label('Id Document')
                                                            ->disabled()
                                                            ->dehydrated() //CAMBIO
                                                            ->required()
                                                            ->columnSpan(3),
                                                    ]),
                                            ])
                                            ->compact(),

                                        //Segunda burbuja: el resto de los campos
                                        Section::make('Details')
                                            ->schema([
                                                Textarea::make('description')
                                                    ->label('Tittle')
                                                    ->required()
                                                    //->maxLength(255),
                                                    //->columnSpanFull(),
                                                    ->columnSpan(['md' => 12]),



                                                Section::make()
                                                    ->columns(12)
                                                    ->schema([
                                                        Select::make('operative_doc_type_id')
                                                                ->label('Document Type')
                                                                ->relationship('docType', 'name')
                                                                ->required()
                                                                ->live()
                                                                ->preload()
                                                                ->columnSpan(3), 
                                                                
                                                        TextInput::make('af_mf')
                                                                ->label(' Service Fee')
                                                                ->required()
                                                                ->suffix('%')                    // solo visual
                                                                ->type('text')                   // ⬅️ evita spinner
                                                                ->inputMode('decimal')           // teclado numérico en móvil
                                                                ->rules(['numeric','min:0','max:100'])  // valida 0–100
                                                                ->extraInputAttributes(['class' => 'text-right tabular-nums'])

                                                                // Mostrar 3.50 cuando en BD hay 0.035
                                                                ->formatStateUsing(fn ($state) =>
                                                                    $state === null ? null : number_format((float)$state * 100, 2, '.', '')
                                                                )

                                                                // Guardar 0.035 cuando el usuario escribe 3.50
                                                                ->dehydrateStateUsing(fn ($state) =>
                                                                    $state === null ? null : round((float) str_replace(',', '', $state) / 100, 6)
                                                                )
                                                                ->columnSpan(3),
                                                    ])
                                                    ->columnSpan(['md' => 12])
                                                    ->compact(),             
                                                    


                                        Section::make('Coverage period')
                                            ->columns(12)
                                            ->schema([
                                                    Placeholder::make('')
                                                        ->content(function (Get $get) {
                                                            $from = $get('inception_date');
                                                            $to   = $get('expiration_date');
                                                            $days = $get('coverage_days');

                                                            $base = 'Select the coverage dates (From–To). The period (days) updates automatically.';
                                                            if ($from && $to && $days) {
                                                                return "$base Current selection: $from → $to ($days days).";
                                                            }
                                                            return "$base End date must be after the start date.";
                                                        })
                                                        ->extraAttributes(['class' => 'text-sm text-gray-800 leading-tight'])
                                                        ->columnSpanFull(),

                                                    DatePicker::make('inception_date')
                                                        ->label('From')
                                                        ->inlineLabel()
                                                        ->required()
                                                        ->displayFormat('d/m/Y')
                                                        ->native(false)
                                                        ->before('expiration_date')
                                                        ->live()
                                                        ->afterStateHydrated(function (Forms\Set $set, $state, Forms\Get $get, $record) {
                                                            if ($record?->inception_date) {
                                                                $set('inception_date', $record->inception_date->format('Y-m-d'));
                                                            }
                                                            $from = $get('inception_date'); $to = $get('expiration_date');
                                                            if ($from && $to) {
                                                                $set('coverage_days', \Carbon\Carbon::parse($from)->diffInDays(\Carbon\Carbon::parse($to)));
                                                            }
                                                        })
                                                        ->afterStateUpdated(function (Forms\Set $set, $state, Forms\Get $get) {
                                                            $from = $state; $to = $get('expiration_date');
                                                            if (! $from || ! $to) return $set('coverage_days', null);
                                                            $f = \Carbon\Carbon::parse($from); $t = \Carbon\Carbon::parse($to);
                                                            if ($f->gte($t)) return $set('coverage_days', null);
                                                            $set('coverage_days', $f->diffInDays($t));
                                                        })
                                                        ->columnSpan(3),

                                                    DatePicker::make('expiration_date')
                                                        ->label('To')
                                                        ->inlineLabel()
                                                        ->required()
                                                        ->displayFormat('d/m/Y')
                                                        ->native(false)
                                                        ->after('inception_date')
                                                        ->live()
                                                        ->afterStateHydrated(function (Forms\Set $set, $state, Forms\Get $get, $record) {
                                                            if ($record?->expiration_date) {
                                                                $set('expiration_date', $record->expiration_date->format('Y-m-d'));
                                                            }
                                                            $from = $get('inception_date'); $to = $get('expiration_date');
                                                            if ($from && $to) {
                                                                $set('coverage_days', \Carbon\Carbon::parse($from)->diffInDays(\Carbon\Carbon::parse($to)));
                                                            }
                                                        })
                                                        ->afterStateUpdated(function (Forms\Set $set, $state, Forms\Get $get) {
                                                            $to = $state; $from = $get('inception_date');
                                                            if (! $from || ! $to) return $set('coverage_days', null);
                                                            $f = \Carbon\Carbon::parse($from); $t = \Carbon\Carbon::parse($to);
                                                            if ($t->lte($f)) return $set('coverage_days', null);
                                                            $set('coverage_days', $f->diffInDays($t));
                                                        })
                                                        ->columnSpan(3),

                                                    // ⬅️ Espaciador de 3 columnas
                                                    Placeholder::make('gap_exp_to_period')
                                                        ->hiddenLabel()
                                                        ->content(new HtmlString('&nbsp;')) // mantiene el ancho sin texto visible
                                                        //->columnSpan(['sm' => 12, 'md' => 3]) // full en móvil, 3 cols desde md
                                                        ->extraAttributes([
                                                            'aria-hidden' => 'true',
                                                            'class' => 'min-h-[1px] p-0 m-0',
                                                        ]) 
                                                        ->columnSpan(3),   

                                                    TextInput::make('coverage_days')
                                                        ->label('Period')
                                                        ->inlineLabel()
                                                        ->numeric()
                                                        ->disabled()
                                                        ->dehydrated(false)
                                                        ->suffix('days')
                                                        ->extraInputAttributes(['class' => 'text-right'])
                                                        ->placeholder('—')
                                                        ->columnSpan(3),
                                                ])
                                            ->columnSpan(['md' => 12])
                                            ->compact(),

                                    ])
                                    ->columns(2)
                                    ->compact(),
                                                     
                                    //Tercera burbuja: solo el archivo
                                    Section::make('File Upload')
                                        ->schema([
                                            FileUpload::make('document_path')
                                                ->label('File')
                                                ->disk('s3')
                                                ->directory('reinsurers/OperativeDocuments')
                                                ->visibility('public')
                                                ->acceptedFileTypes(['application/pdf'])
                                                ->preserveFilenames(false)
                                                ->saveUploadedFileUsing(function (TemporaryUploadedFile $file, $record, Get $get) {
                                                    $base = (string) ($get('id') ?: $record?->id);
                                                    $name = $base . '.' . ($file->getClientOriginalExtension() ?: 'pdf');
                                                    $dir  = 'reinsurers/OperativeDocuments';
                                                    Storage::disk('s3')->putFileAs($dir, $file, $name, ['visibility' => 'public']);
                                                    return "{$dir}/{$name}"; // <- esto se guarda en document_path
                                                })
                                                ->deleteUploadedFileUsing(function (?string $file) {
                                                    if ($file && Storage::disk('s3')->exists($file)) {
                                                        Storage::disk('s3')->delete($file);
                                                    }
                                                })
                                                ->downloadable()
                                                ->openable()
                                                ->previewable(true)
                                                ->hint(fn ($record) => $record?->document_path
                                                    ? 'Existing file: ' . basename($record->document_path)
                                                    : 'No file uploaded yet.'
                                                )
                                                ->dehydrated(fn ($state) => filled($state))
                                                ->helperText('Only PDF files are allowed.'),


                                            /* FileUpload::make('document_path')
                                                ->label('File')
                                                ->disk('s3')
                                                ->directory('reinsurers/OperativeDocuments')
                                                ->visibility('public')
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
                                                ->helperText('Only PDF files are allowed.'), */

                                        ])
                                        ->compact(),

                                    ]),
                                //--- End Tab -------------------------------------------     

                                
                                //-------------------------------------------------------    
                                //🟢 2.-Tab for Placement Schemes.  
                                //-------------------------------------------------------
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
                                //--- End Tab ----------------------------------------     


                                //----------------------------------------------------        
                                //🟡 3.-Tab for Insured Members.  
                                //----------------------------------------------------
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
                                //--- End Tab ----------------------------------------          


                        
                                //----------------------------------------------------        
                                //⚪️ 4.-Tab for Installments 
                                //----------------------------------------------------                                                                                                                               
                                Tab::make('Installments')
                                    ->icon('heroicon-o-banknotes')
                                    ->reactive()
                                    ->live()
                                    ->schema([

                                        // 🟡 nonce para forzar re-render del grid (no se guarda en BD)
                                        Hidden::make('logs_nonce')
                                            ->default(0)
                                            ->reactive()
                                            ->dehydrated(false),

                                        Repeater::make('transactions')
                                            ->label('Installments')
                                            ->relationship()
                                            ->schema([
                                                Hidden::make('id')
                                                    ->dehydrated()
                                                    ->dehydrateStateUsing(fn ($state) => $state ?: null), // 🛠️ CHG: '' → null para no mandar id vacío

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
                                                    // 🟡 Dispara solo al confirmar (no por cada tecla)
                                                    ->live(onBlur: true)
                                                    ->minValue(0)
                                                    ->maxValue(100)
                                                    ->step(0.01)
                                                    ->mask(RawJs::make('$money($input, ".", ",", 2)'))
                                                    ->reactive()
                                                    ->formatStateUsing(fn ($state) => $state !== null ? round($state * 100, 2) : null) // decimal → %
                                                    ->dehydrateStateUsing(fn ($state) => floatval(str_replace(',', '', $state)) / 100) // % → decimal
                                                    // 🟡 Si hay alguna fila completa, refrescamos el preview
                                                    ->afterStateUpdated(function (\Filament\Forms\Set $set, \Filament\Forms\Get $get) {
                                                        $rows = collect($get('transactions') ?? []);
                                                        $isAnyComplete = $rows->contains(function ($r) {
                                                            $prop = $r['proportion'] ?? null;
                                                            if (is_string($prop)) $prop = floatval(str_replace(',', '', $prop));
                                                            if ($prop !== null && $prop > 1) $prop = $prop / 100;
                                                            $rate = isset($r['exch_rate']) ? (float) $r['exch_rate'] : null;
                                                            $due  = $r['due_date'] ?? null;
                                                            // 🟡 Validar fecha real en formato Y-m-d (evita contar placeholders tipo dd/mm/yyyy)
                                                            $dueOk = is_string($due) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $due);
                                                            return $prop !== null && $rate && $rate > 0 && $dueOk;
                                                        });
                                                        if ($isAnyComplete) $set('logs_nonce', ($get('logs_nonce') ?? 0) + 1);
                                                    })
                                                    ->columnSpan(1),

                                                TextInput::make('exch_rate')
                                                    ->label('Exchange Rate')
                                                    ->numeric()
                                                    ->required()
                                                    // 🟡 Dispara solo al confirmar
                                                    ->live(onBlur: true)
                                                    ->step(0.00001)
                                                    // 🟡 Refresca únicamente si hay una fila completa
                                                    ->afterStateUpdated(function (\Filament\Forms\Set $set, \Filament\Forms\Get $get) {
                                                        $rows = collect($get('transactions') ?? []);
                                                        $isAnyComplete = $rows->contains(function ($r) {
                                                            $prop = $r['proportion'] ?? null;
                                                            if (is_string($prop)) $prop = floatval(str_replace(',', '', $prop));
                                                            if ($prop !== null && $prop > 1) $prop = $prop / 100;
                                                            $rate = isset($r['exch_rate']) ? (float) $r['exch_rate'] : null;
                                                            $due  = $r['due_date'] ?? null;
                                                            $dueOk = is_string($due) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $due);
                                                            return $prop !== null && $rate && $rate > 0 && $dueOk;
                                                        });
                                                        if ($isAnyComplete) $set('logs_nonce', ($get('logs_nonce') ?? 0) + 1);
                                                    })
                                                    ->columnSpan(1),

                                                DatePicker::make('due_date')
                                                    ->label('Due Date')
                                                    ->required()
                                                    // 🟡 Dispara solo al cerrar/confirmar
                                                    ->live(onBlur: true)
                                                    // 🟡 Refresca únicamente si hay una fila completa
                                                    ->afterStateUpdated(function (\Filament\Forms\Set $set, \Filament\Forms\Get $get) {
                                                        $rows = collect($get('transactions') ?? []);
                                                        $isAnyComplete = $rows->contains(function ($r) {
                                                            $prop = $r['proportion'] ?? null;
                                                            if (is_string($prop)) $prop = floatval(str_replace(',', '', $prop));
                                                            if ($prop !== null && $prop > 1) $prop = $prop / 100;
                                                            $rate = isset($r['exch_rate']) ? (float) $r['exch_rate'] : null;
                                                            $due  = $r['due_date'] ?? null;
                                                            $dueOk = is_string($due) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $due);
                                                            return $prop !== null && $rate && $rate > 0 && $dueOk;
                                                        });
                                                        if ($isAnyComplete) $set('logs_nonce', ($get('logs_nonce') ?? 0) + 1);
                                                    })
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




                                            // ✅ Guardado MANUAL: no borrar hijos implícitamente, actualizar existentes y crear nuevos
                                            ->saveRelationshipsUsing(function (\Filament\Forms\Components\Repeater $component, ?array $state) {
   
                                                $state = $state ?? []; // 🔧

                                                $relation = $component->getRelationship();   // HasMany transactions()
                                                $parent   = $relation->getParent();          // OperativeDoc dueño
                                                $query    = $relation->getQuery();           // Builder transactions

                                                // Helpers de normalización
                                                $parseFloat = function ($v) {
                                                    if ($v === null || $v === '') return null;
                                                    if (is_string($v)) $v = str_replace([',', ' '], '', $v);
                                                    return is_numeric($v) ? (float) $v : null;
                                                };

                                                // Acepta 3.5 → 0.035 y 35 → 0.35 (igual que tu lógica de preview)
                                                $parsePercentishToDecimal = function ($v) use ($parseFloat) {
                                                    $n = $parseFloat($v);
                                                    if ($n === null) return null;
                                                    return $n > 1 ? $n / 100 : $n;
                                                };

                                                // 1) Hijos existentes (activos; SoftDeletes excluye los borrados)
                                                $existing    = $query->get()->keyBy('id');
                                                $existingIds = $existing->keys();

                                                // 2) Estado entrante
                                                $incoming    = collect($state ?? [])->values();
                                                $incomingIds = $incoming->pluck('id')->filter()->values();

                                                // 3) Borrados manuales (los que ya no vienen)
                                                $idsToDelete = $existingIds->diff($incomingIds);
                                                if ($idsToDelete->isNotEmpty()) {
                                                    $query->whereIn('id', $idsToDelete->all())
                                                        ->get()
                                                        ->each(fn ($m) => $m->delete()); // Soft delete + eventos (reindex en booted)
                                                }

                                                // 4) Actualizar existentes
                                                $incoming->filter(fn ($row) => !empty($row['id']))
                                                    ->each(function ($row) use ($existing, $parsePercentishToDecimal, $parseFloat) {
                                                        $id = $row['id'];
                                                        if (! $existing->has($id)) return;

                                                        // Normalizaciones
                                                        $propDec  = $parsePercentishToDecimal($row['proportion'] ?? null);
                                                        $exchRate = $parseFloat($row['exch_rate'] ?? null);

                                                        // Validaciones server-side
                                                        if ($propDec !== null && ($propDec < 0 || $propDec > 1)) {
                                                            throw ValidationException::withMessages([
                                                                "transactions" => "Installment #".($row['index'] ?? '?').": Proportion debe estar entre 0% y 100%.",
                                                            ]);
                                                        }

                                                        $existing[$id]->fill([
                                                            'index'                 => $row['index'] ?? null,
                                                            'proportion'            => $propDec,     // 👈 guardamos DECIMAL (0–1)
                                                            'exch_rate'             => $exchRate,
                                                            'due_date'              => $row['due_date'] ?? null,
                                                            'remmitance_code'       => $row['remmitance_code'] ?? null,
                                                            'transaction_type_id'   => $row['transaction_type_id'] ?? 1,
                                                            'transaction_status_id' => $row['transaction_status_id'] ?? 1,
                                                            // op_document_id se mantiene
                                                        ])->save();
                                                    });

                                                // 5) Crear nuevos
                                                $toCreate = $incoming->filter(fn ($row) => empty($row['id']))
                                                    ->map(function ($row) use ($parent, $parsePercentishToDecimal, $parseFloat) {
                                                        $propDec  = $parsePercentishToDecimal($row['proportion'] ?? null);
                                                        $exchRate = $parseFloat($row['exch_rate'] ?? null);

                                                        if ($propDec !== null && ($propDec < 0 || $propDec > 1)) {
                                                            throw \Illuminate\Validation\ValidationException::withMessages([
                                                                "transactions" => "Installment #".($row['index'] ?? '?').": Proportion debe estar entre 0% y 100%.",
                                                            ]);
                                                        }

                                                        return [
                                                            'index'                 => $row['index'] ?? null,
                                                            'proportion'            => $propDec,     // 👈 DECIMAL (0–1)
                                                            'exch_rate'             => $exchRate,
                                                            'due_date'              => $row['due_date'] ?? null,
                                                            'remmitance_code'       => $row['remmitance_code'] ?? null,
                                                            'transaction_type_id'   => $row['transaction_type_id'] ?? 1,
                                                            'transaction_status_id' => $row['transaction_status_id'] ?? 1,
                                                            'op_document_id'        => $parent->getKey(),
                                                        ];
                                                    })->all();

                                                if (!empty($toCreate)) {
                                                    $relation->createMany($toCreate);
                                                }
                                            })






                                            // 🛠️ Hooks por ítem (conservados)
                                            ->mutateRelationshipDataBeforeCreateUsing(function (array $data): array {
                                                // Si por alguna razón el ítem nuevo trae 'id' (clonado), lo quitamos
                                                if (array_key_exists('id', $data) && $data['id'] !== null) {
                                                    unset($data['id']);
                                                }
                                                return $data;
                                            })

                                            ->mutateRelationshipDataBeforeSaveUsing(function (array $data): array {
                                                // Normaliza id vacío a null para evitar confusiones
                                                if (($data['id'] ?? null) === '') {
                                                    unset($data['id']);
                                                }
                                                return $data;
                                            })






            
                                            // 🟡 Al AGREGAR: reindexa y sube el nonce
                                            ->addAction(fn (\Filament\Forms\Components\Actions\Action $action) =>
                                                $action->label('New Installment')
                                                    ->after(function (\Filament\Forms\Set $set, \Filament\Forms\Get $get) {
                                                        $tx = collect($get('transactions') ?? [])
                                                            ->values()
                                                            ->map(function ($row, $i) {
                                                                $row['index'] = $i + 1;
                                                                return $row;
                                                            })->all();

                                                        // Limpiar ids duplicados si se clonó una fila
                                                        $seen = [];
                                                        foreach ($tx as &$row) {
                                                            if (!empty($row['id'])) {
                                                                if (isset($seen[$row['id']])) {
                                                                    $row['id'] = null; // fuerza INSERT sin PK duplicada
                                                                } else {
                                                                    $seen[$row['id']] = true;
                                                                }
                                                            }
                                                        }
                                                        unset($row);

                                                        $set('transactions', $tx);
                                                        $set('logs_nonce', ($get('logs_nonce') ?? 0) + 1);
                                                    })
                                            )

                                            // 🟡 Al ELIMINAR: reindexa y refresca inmediatamente el grid
                                            ->deleteAction(fn (\Filament\Forms\Components\Actions\Action $action) =>
                                                $action->after(function (\Filament\Forms\Set $set, \Filament\Forms\Get $get) {
                                                    $tx = collect($get('transactions') ?? [])
                                                        ->values()
                                                        ->map(function ($row, $i) {
                                                            $row['index'] = $i + 1;
                                                            return $row;
                                                        })->all();

                                                    $set('transactions', $tx);
                                                    $set('logs_nonce', ($get('logs_nonce') ?? 0) + 1);
                                                })
                                            ),

                                        // ⬆️ ─── END Repeater






                                        // ⬇️ ─── Section: Installments Log (PREVIEW en vivo con el estado del form)
                                        /* Section::make('Installments Log')
                                            ->schema([
                                                View::make('partials.transaction-logs-grid')
                                                    ->reactive()
                                                    // 🟡 Reconcilia el DOM únicamente cuando cambia el nonce
                                                    ->key(fn (Get $get) => ['wire:key' => 'logs-grid-'.$get('logs_nonce')])
                                                    ->viewData(function (Get $get, $record) {
                                                        // toques para reactividad
                                                        $touchTx  = $get('transactions');
                                                        $touchNce = $get('logs_nonce');

                                                        $docId = $record?->id ?? $get('id');
                                                        if (! $docId) return ['rows' => collect()];

                                                        // 🟡 Tomar el estado actual del repeater
                                                        $stateTx   = collect($get('transactions') ?? []);

                                                        // 🟡 Solo transacciones PERSISTIDAS que siguen presentes en el formulario
                                                        $persisted = $stateTx->filter(fn ($it) => !empty($it['id']))->values();

                                                        // 🟡 Mapa de índice preferente desde el estado (respeta reordenamiento antes de guardar)
                                                        $indexByTxnIdFromState = $persisted->mapWithKeys(
                                                            fn ($it) => [$it['id'] => (int)($it['index'] ?? 0)]
                                                        );

                                                        // ===== A) LOGS REALES DESDE BD PERO SOLO DE LOS IDs QUE SIGUEN EN EL FORM =====
                                                        $rowsPersisted = collect();
                                                        if ($persisted->isNotEmpty()) {
                                                            $keptIds = $persisted->pluck('id');

                                                            $rowsPersisted = \App\Models\TransactionLog::with(['deduction', 'fromPartner', 'toPartner'])
                                                                ->whereIn('transaction_id', $keptIds)   // 🟡 clave: ya NO usamos “todas las del doc”
                                                                ->get()
                                                                ->map(function ($log) use ($indexByTxnIdFromState) {
                                                                    return [
                                                                        'inst_index'  => (int) ($indexByTxnIdFromState[$log->transaction_id] ?? 0),
                                                                        'index'       => (int) ($log->index ?? 0),
                                                                        'deduction'   => $log->deduction?->concept ?? '-',
                                                                        'from'        => $log->fromPartner?->short_name ?? '-',
                                                                        'to'          => $log->toPartner?->short_name ?? '-',
                                                                        'exch_rate'   => $log->exch_rate,
                                                                        'gross'       => $log->gross_amount,
                                                                        'discount'    => $log->commission_discount,
                                                                        'banking_fee' => $log->banking_fee,
                                                                        'net'         => $log->net_amount,
                                                                        'status'      => $log->status,
                                                                    ];
                                                                });
                                                        }

                                                        // 2) PREVIEW solo para filas NUEVAS y COMPLETAS del estado del form
                                                        $stateTx = collect($get('transactions') ?? []);

                                                        $txForPreview = $stateTx
                                                            ->filter(fn ($r) => empty($r['id'])) // solo las nuevas
                                                            ->map(function ($t) {
                                                                $prop = $t['proportion'] ?? null;
                                                                $prop = is_string($prop) ? floatval(str_replace(',', '', $prop)) : $prop;
                                                                if ($prop !== null && $prop > 1) $prop = $prop / 100;
                                                                return [
                                                                    'index'      => (int)($t['index'] ?? 0),
                                                                    'proportion' => $prop,
                                                                    'exch_rate'  => isset($t['exch_rate']) ? (float) $t['exch_rate'] : null,
                                                                    'due_date'   => $t['due_date'] ?? null,
                                                                ];
                                                            })
                                                            // 🟡 Consideramos “completa” solo con fecha real Y-m-d y rate > 0
                                                            ->filter(function ($r) {
                                                                $dueOk = is_string($r['due_date']) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $r['due_date']);
                                                                return $r['proportion'] !== null && ($r['exch_rate'] ?? 0) > 0 && $dueOk;
                                                            })
                                                            ->sortBy('index')
                                                            ->values()
                                                            ->all();

                                                        $rowsPreview = collect();
                                                        if (!empty($txForPreview)) {
                                                            $schemeIds = collect($get('schemes') ?? [])->pluck('cscheme_id')->filter()->values()->all();
                                                            $insureds   = $get('insureds') ?? [];

                                                            $rowsPreview = app(\App\Services\TransactionLogBuilder::class)
                                                                ->previewForOperativeDocState($docId, [
                                                                    'transactions' => $txForPreview,
                                                                    'schemes'      => $schemeIds,
                                                                    'insureds'     => $insureds,
                                                                ])
                                                                ->map(fn ($r) => array_merge($r, ['status' => 'preview']));
                                                        }

                                                        // 3) Combinamos BD + PREVIEW completo, ordenado
                                                        $rows = $rowsPersisted
                                                            ->concat($rowsPreview ?? collect())
                                                            ->sortBy([['inst_index','asc'], ['index','asc']])
                                                            ->values();

                                                        return ['rows' => $rows];
                                                    }),
                                            ])
                                            ->compact()
                                            ->extraAttributes([
                                                'class' => 'rounded-xl ring-1 ring-gray-950/10 dark:ring-white/10 bg-transparent p-4',
                                            ]),  // ⬆️ ─── END Section*/

                                    ]), //─── End Tab ─────────────────────────────────────────── 






 // 👉 BOTÓN para abrir el recurso de logs filtrado por este documento
                                        /* Actions::make([
                                            FormAction::make('rebuildLogs')
                                                ->label('Build / Update logs')
                                                ->icon('heroicon-m-arrow-path')
                                                ->color('primary')
                                                ->requiresConfirmation()
                                                ->modalHeading('Rebuild transaction logs')
                                                ->modalDescription('This will (re)build the transaction logs from the current Placement Schemes and Installments. Operational fields (sent/received/banking fee/status) will be preserved when possible.')
                                                ->modalSubmitActionLabel('Run')
                                                ->action(function (Get $get) {
                                                    $docId = $get('id');

                                                    if (! $docId) {
                                                        Notification::make()
                                                            ->title('No document ID found')
                                                            ->danger()
                                                            ->send();
                                                        return;
                                                    }

                                                    try {
                                                        $affected = app(TransactionLogBuilder::class)->rebuildForOperativeDoc($docId);

                                                        Notification::make()
                                                            ->title("Transaction logs built/updated ({$affected})")
                                                            ->success()
                                                            ->send();
                                                    } catch (\Throwable $e) {
                                                        report($e);

                                                        Notification::make()
                                                            ->title('Could not rebuild logs')
                                                            ->body($e->getMessage())
                                                            ->danger()
                                                            ->send();
                                                    }
                                                }),
                                        ]), */













                                      
                            
                        ])
                        ->columnSpanFull(), //─────── END tabs ──────────────────────────────────
                                                     
                ]), //──────── END Section ──────────────────────────────────────────────────────────

                



                   
                // ─────────  B) SECTION: (colapsable)  ─────────────────────────────────────────
                // 🟡 SPACE 
                // ──────────────────────────────────────────────────────────────────────────────
                Placeholder::make('')
                    ->content('')
                    ->columnSpanFull()
                    ->extraAttributes(['class' => 'my-1']), // 👈 margen vertical
                // ─── END Section ──────────────────────────────────────────────────────────────
            

                





        
                // ─────────  C) SECTION: (colapsable)  ───────────────────────────────────────── 
                // 🟡 SUMMARY Section
                // ──────────────────────────────────────────────────────────────────────────────
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
                                    ->map(fn ($scheme) => \App\Models\CostScheme::with('costNodexes.costScheme', 'costNodexes.partnerSource', 'costNodexes.deduction') // <-------
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
                                                'partner' => $node->partnerSource?->name ?? '-',
                                                'partner_short' => $node->partnerSource?->short_name
                                                                   ?? ($node->partnerSource?->name
                                                                   ?? '-'),
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


                                    // 👇 NUEVO: logs persistidos por transacción e índice
                                    $persistedTxIds = collect($get('transactions') ?? [])->pluck('id')->filter()->values();
                                    $logsByTxn = [];

                                    if ($persistedTxIds->isNotEmpty()) {
                                        $logs = \App\Models\TransactionLog::with('toPartner')
                                            ->whereIn('transaction_id', $persistedTxIds)
                                            ->get();

                                        $logsByTxn = $logs->groupBy('transaction_id')->map(function ($grp) {
                                            return $grp->mapWithKeys(function ($log) {
                                                $idx = (int)($log->index ?? 0);
                                                return [
                                                    $idx => [
                                                        'to_short'   => $log->toPartner?->short_name
                                                                        ?? $log->toPartner?->name
                                                                        ?? '-',
                                                        'to_full'    => $log->toPartner?->name ?? '-',
                                                        'exch_rate'  => $log->exch_rate,
                                                        'gross'      => $log->gross_amount,
                                                        'discount'   => $log->commission_discount,
                                                        'banking'    => $log->banking_fee,
                                                        'net'        => $log->net_amount,
                                                        'status'     => $log->status,
                                                    ],
                                                ];
                                            });
                                        })->toArray();
                                    }







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
                                    'logsByTxn'            => $logsByTxn,  // 👈 NUEVO
                                ];
                            }),
                    ])
                    ->columnSpanFull(),
                    //--------🟡 End Section SUMMARY -----------------------------------------------
                    
                         

        ]);
    }












    // ──────  CRUD LIST  ─────────────────────────
    public function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('index')
                ->sortable()
                ->verticalAlignment(VerticalAlignment::Start) 
                ->sortable()
                ->searchable(),

            Tables\Columns\TextColumn::make('id')
                ->label('Document code')
                ->sortable()
                ->verticalAlignment(VerticalAlignment::Start) 
                ->copyable()
                ->sortable()
                ->searchable()
                ->tooltip(fn ($state) => $state) 
                ->extraAttributes(['class' => 'w-64']), // 👈 Ajusta el ancho

            Tables\Columns\TextColumn::make('docType.name')
                ->label('Doc Type')
                ->sortable()
                ->verticalAlignment(VerticalAlignment::Start) 
                ->sortable()
                ->searchable(),

            Tables\Columns\TextColumn::make('description')
                ->searchable()
                ->sortable()
                ->verticalAlignment(VerticalAlignment::Start) 
                ->wrap() // 👈 permite múltiples líneas
                ->extraAttributes([
                        'style' => 'width: 250px; white-space: normal; vertical-align: top;',
                    ]),

            Tables\Columns\TextColumn::make('inception_date')
                ->sortable()
                ->verticalAlignment(VerticalAlignment::Start)   
                ->date(),

            Tables\Columns\TextColumn::make('expiration_date')
                ->sortable()
                ->verticalAlignment(VerticalAlignment::Start) 
                ->date(),
            
            Tables\Columns\TextColumn::make('status')
                ->label('Status')
                ->sortable()
                ->verticalAlignment(VerticalAlignment::Start) 
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
                ->sortable()
                ->verticalAlignment(VerticalAlignment::Start) 
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
                ->modalHeading('➕ Create Operative Doc')
                ->modalWidth('7xl')

                ->createAnother(false)                 // 👈 oculta "Create & create another"
                ->modalSubmitActionLabel('Create')     // 👈 botón principal
                ->modalCancelActionLabel('Cancel')     // 👈 botón cancelar


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
                
            // ⬇️ NUEVO: reconstruir logs tras guardar y commitear
                ->after(function ($record, array $data) {
                    DB::afterCommit(function () use ($record) {
                        try {
                            $affected = app(TransactionLogBuilder::class)
                                ->rebuildForOperativeDoc($record->id);

                            Notification::make()
                                ->title("Transaction logs built/updated ({$affected})")
                                ->success()
                                ->send();
                        } catch (\Throwable $e) {
                            report($e);

                            Notification::make()
                                ->title('Could not rebuild logs')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    });
                }),


        ])
        ->actions([
            Tables\Actions\ActionGroup::make([
                Tables\Actions\ViewAction::make('view')
                    ->label('View')
                    ->modalHeading(fn ($record) => '📄 Reviewing ' . $record->docType->name .' — '. $record->id )
                    ->modalWidth('7xl'),  

                /* Tables\Actions\EditAction::make('edit')
                    ->label('Edit')
                    ->modalHeading(fn ($record) => '📝 Modifying ' . $record->docType->name .' — '. $record->id )
                    ->modalWidth('6xl'), 

                Tables\Actions\DeleteAction::make(),
            ]),
        ]) */
        

            Tables\Actions\EditAction::make('edit')
                ->label('Edit')
                ->modalHeading(fn ($record) => '📝 Modifying ' . $record->docType->name .' — '. $record->id )
                ->modalWidth('7xl')
                // ⬇️ NUEVO: reconstruir logs tras guardar y commitear
                ->after(function ($record, array $data) {
                    DB::afterCommit(function () use ($record) {
                        try {
                            $affected = app(TransactionLogBuilder::class)
                                ->rebuildForOperativeDoc($record->id);

                            Notification::make()
                                ->title("Transaction logs built/updated ({$affected})")
                                ->success()
                                ->send();
                        } catch (\Throwable $e) {
                            report($e);

                            Notification::make()
                                ->title('Could not rebuild logs')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    });
                }),

            Tables\Actions\DeleteAction::make(),
        ]),
    ])    
        
        
        
        
        ->bulkActions([
            //Tables\Actions\DeleteBulkAction::make(),
        ]);
    }
}
