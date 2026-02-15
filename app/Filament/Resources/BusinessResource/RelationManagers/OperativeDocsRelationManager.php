<?php

namespace App\Filament\Resources\BusinessResource\RelationManagers;


use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;           // 👈 importa la facade
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\FileUpload;
use Filament\Support\Enums\VerticalAlignment;
use Filament\Support\RawJs;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\View;
use Filament\Forms\Components\Hidden;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Forms\Get;
use Carbon\Carbon;
use Filament\Notifications\Notification;
use Filament\Forms\Components\Actions\Action as FormAction;
use Illuminate\Support\Facades\DB;
use App\Services\TransactionLogBuilder;
use Illuminate\Support\HtmlString;
use Illuminate\Validation\ValidationException;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use App\Models\Traits\HasOperativeDocOverview;
use Filament\Forms\Components\ToggleButtons;
use App\Models\CostScheme;
use App\Models\CostNodex;
use Filament\Tables\Actions\Action;
use Filament\Facades\Filament;
use App\Services\OperativeDocSummaryV2Service;
use Filament\Forms\Set;
use Filament\Forms\Components\Actions\Action as HeaderAction;
use Illuminate\Support\Str;
use App\Models\Coverage;
use App\Models\Business;
use Filament\Tables\Actions\Action as TableAction;
use Filament\Actions\StaticAction; 
use Filament\Forms\Components\Alert;




class OperativeDocsRelationManager extends RelationManager
{
    use HasOperativeDocOverview;
    protected static string $relationship = 'OperativeDocs';
    protected static ?string $title = 'Operative Documents';
    protected static ?string $icon = 'heroicon-o-document-text';
    protected static ?string $recordTitleAttribute = 'description';

     public static function getCreateFormHeading(): string
    {
        return 'New Operative Document';
    }

    public static function getEditFormHeading(): string
    {
        return 'Edit Operative Document';
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with([
            'docType',
            'business',
            'schemes.costScheme.costNodexes', 
            //'transactions',
        ]);
    }

    // ✅ CAMBIO 1: helpers SIN $livewire (StaticAction NO puede resolverlo)
    protected function getModalActivePanel(): string
    {
        // Estado actual del form del modal (Create/Edit)
        $state = $this->getMountedTableActionForm()?->getRawState() ?? [];

        return (string) ($state['active_panel'] ?? 'tabs');
    }

    protected function isOverviewActive(): bool
    {
        return $this->getModalActivePanel() === 'summary';
    }


    


    // ──────FORM CREATE / EDIT  ─────────────────────────
    public function form(Form $form): Form
    {
        return $form->schema([

                // 🟡 Controla Tabs vs Summary
                Hidden::make('active_panel')
                    ->default('tabs')   // 👈 por defecto Tabs abierto, Summary cerrado
                    ->live()  
                    ->reactive()
                    ->dehydrated(false), 
                /* ->afterStateHydrated(function (Forms\Set $set, $state) {
                        if (blank($state)) $set('active_panel', 'tabs');
                    }), */
                // 🟢 🔑 VERSIONADOR de Placement Schemes (NO se guarda en BD)
                Hidden::make('schemes_version')
                    ->default(fn () => (string) \Illuminate\Support\Str::uuid())
                    ->reactive()
                    ->dehydrated(false),

                Hidden::make('coverage_helper_tick')
                    ->default((string) Str::uuid())
                    ->dehydrated(false)
                    ->reactive(),    
                 

                // ────────  A) SECTION: TABS (colapsable)  ─────────────────────────
                /* Section::make('Document Details')
                    ->collapsible()
                    ->collapsed(fn (Get $get) => $get('active_panel') !== 'tabs')
                    
                    ->extraAttributes([
                        'x-on:click.self' => '$wire.set("data.active_panel","tabs"); $wire.set("active_panel","tabs");',
                    ]) */
                Section::make('Primary Document Data')
                    ->visible(fn (Get $get) => ($get('active_panel') ?? 'tabs') === 'tabs')
                    ->headerActions([
                        HeaderAction::make('goSummary')
                            ->label('Overview')
                            ->icon('heroicon-o-eye')
                            ->action(fn (Set $set) => $set('active_panel', 'summary')),
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
                                        /* Section::make()
                                            ->schema([*/
                                        Section::make('General Information')
                                                    ->columns(12)
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
                                           /* ])
                                            ->compact(), */

                                        //Segunda burbuja: el resto de los campos
                                        /* Section::make('General Information')
                                            ->schema([ */

                                          
                                                Textarea::make('description')
                                                    ->label('Description')
                                                    ->required()
                                                    ->placeholder('Fill in the document description')
                                                    ->dehydratedWhenHidden() 
                                                    ->autosize()
                                                    ->columnSpan(['md' => 12]),



                                                /* Section::make('')
                                                    ->columns(12)
                                                    ->schema([ */

                                                        Placeholder::make('')
                                                        ->content('Please select the document type first, then enter the service fee to be charged (Management Fee or Access Fee).')
                                                        ->extraAttributes([
                                                            'class' => 'text-sm text-gray-600 dark:text-gray-400 text-left'
                                                        ])
                                                        ->columnSpanFull()
                                                        ->hiddenOn('view'),

                                                        Select::make('operative_doc_type_id')
                                                            ->label('Document Type')
                                                            ->relationship(
                                                                name: 'docType',
                                                                titleAttribute: 'name',
                                                                modifyQueryUsing: function (Builder $query) {
                                                                    $query->orderBy('id');

                                                                    // ✅ CAMBIO: si Slip ya existe, ocultar opción 1 del selector (solo en CREATE)
                                                                    $record = $this->getMountedTableActionRecord(); // null = create, no-null = edit
                                                                    if ($record) {
                                                                        return;
                                                                    }

                                                                    $business = $this->getOwnerRecord();

                                                                    $slipExists = $business?->operativeDocs()
                                                                        ->where('operative_doc_type_id', 1)
                                                                        ->exists() ?? false;

                                                                    if ($slipExists) {
                                                                        $query->where('id', '!=', 1);
                                                                    }
                                                                }
                                                            )
                                                            ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->id} - {$record->name}")
                                                            ->required()
                                                            ->dehydrated() // ✅ aunque esté disabled, manda el valor
                                                            ->live()
                                                            ->preload()
                                                            ->columnSpan(3)
                                                            ->default(function () {
                                                                $business = $this->getOwnerRecord();

                                                                $slipExists = $business?->operativeDocs()
                                                                    ->where('operative_doc_type_id', 1)
                                                                    ->exists() ?? false;

                                                                return $slipExists ? null : 1;
                                                            })
                                                            ->afterStateHydrated(function (Set $set, $record, $state) {
                                                                if ($record) return; // Edit: no tocar

                                                                if (blank($state)) {
                                                                    $set('operative_doc_type_id', 1); // ✅ asegura state para que se dehydrate
                                                                }
                                                            })
                                                            ->disabled(function ($record) {
                                                                if ($record) return true;

                                                                $business = $this->getOwnerRecord();

                                                                $slipExists = $business?->operativeDocs()
                                                                    ->where('operative_doc_type_id', 1)
                                                                    ->exists() ?? false;

                                                                return ! $slipExists; // primer doc => slip bloqueado
                                                            }), 
                                                                
                                                        Placeholder::make('gap1')
                                                            ->hiddenLabel()
                                                            ->columnSpan(3),

                                                        
                                                        TextInput::make('roe_fs')
                                                            ->label('Exchange rate')
                                                            ->required()
                                                            ->inputMode('decimal')
                                                            ->rules(['numeric', 'min:0'])
                                                            ->extraInputAttributes(['class' => 'text-right tabular-nums'])

                                                            // 🔒 Bloquear edición si moneda del Business es USD (157)
                                                            ->readOnly(fn ($livewire) =>
                                                                method_exists($livewire, 'getOwnerRecord')
                                                                && (int) $livewire->getOwnerRecord()?->currency_id === 157
                                                            )

                                                            // ✅ MOSTRAR 1.00000000 si es USD aunque el state sea null
                                                            ->formatStateUsing(function ($state, $livewire) {
                                                                $isUsd = method_exists($livewire, 'getOwnerRecord')
                                                                    && (int) $livewire->getOwnerRecord()?->currency_id === 157;

                                                                if ($state === null || $state === '') {
                                                                    return $isUsd ? number_format(1, 8, '.', '') : null;
                                                                }

                                                                return number_format((float) $state, 8, '.', '');
                                                            })

                                                            // ✅ GUARDAR 1 si es USD y viene vacío; si no, null
                                                            ->dehydrateStateUsing(function ($state, $livewire) {
                                                                $isUsd = method_exists($livewire, 'getOwnerRecord')
                                                                    && (int) $livewire->getOwnerRecord()?->currency_id === 157;

                                                                if ($state === null || $state === '') {
                                                                    return $isUsd ? 1 : null;
                                                                }

                                                                return round((float) str_replace(',', '', $state), 8);
                                                            })

                                                            ->dehydrated()
                                                            ->columnSpan(3),

                                                        


                                                        TextInput::make('af_mf')
                                                            ->label('Service Fee')
                                                            ->default(0) // default cero
                                                            //->helpertext('Enter the service amount (Management Fee or Access Fee).')
                                                            ->required()
                                                            ->prefix('$')
                                                            ->type('text')                   
                                                            ->inputMode('decimal')           
                                                            ->rules(['numeric', 'min:0'])    // solo positivos, sin máximo
                                                            ->extraInputAttributes(['class' => 'text-right tabular-nums'])

                                                            // 👉 Mostrar siempre con 2 decimales
                                                            ->formatStateUsing(fn ($state) =>
                                                                $state === null ? '0.00' : number_format((float) $state, 2, '.', '')
                                                            )

                                                            // 👉 Guardar con 2 decimales (sin dividir entre 100)
                                                            ->dehydrateStateUsing(fn ($state) =>
                                                                $state === null || $state === ''
                                                                    ? 0.00
                                                                    : round((float) str_replace(',', '', $state), 2)
                                                            )

                                                            ->columnSpan(3),


                                                    ])
                                                    ->columnSpan(['md' => 12])
                                                    ->compact(),             
                                                    

                                     // ───── Columna 2: Vacía ─────
                                    Placeholder::make('spacer')
                                        ->label(' ')
                                        ->content(' ')
                                        ->columnSpan(3),
                                        

                                    /* ])
                                    ->columns(2)
                                    ->compact(), */

                                    Section::make('Coverage Period')
                                            ->columns(12)
                                            ->schema([
                                                    Placeholder::make('coverage_period_hint')
                                                        ->label(' ')
                                                        ->content(function (Get $get) {
                                                            $from = $get('inception_date');
                                                            $to   = $get('expiration_date');
                                                            $days = $get('coverage_days');

                                                            $base = 'Select the coverage dates (From–To). The coverage period in days is calculated automatically.';
                                                            if ($from && $to && $days) {
                                                                return "$base Current selection: $from → $to ($days days).";
                                                            }
                                                            return "$base The end date must be later than the start date.";
                                                        })
                                                        ->extraAttributes(['class' => 'text-sm text-gray-600 dark:text-gray-400 leading-tight'])
                                                        ->columnSpanFull()
                                                        ->hiddenOn('view'),

                                                    DatePicker::make('inception_date')
                                                        ->label('From')
                                                        ->inlineLabel()
                                                        ->required()
                                                        ->dehydratedWhenHidden() 
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
                                                        ->dehydratedWhenHidden() 
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
                                                     
                                    
                                     // ───── Columna 2: Vacía ─────
                                    Placeholder::make('spacer')
                                        ->label(' ')
                                        ->content(' ')
                                        ->columnSpan(3),
                                    
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

                                                // 1) Subida con nombre estable (basado en id) y limpieza del anterior si cambia
                                                ->saveUploadedFileUsing(function (TemporaryUploadedFile $file, $record, Get $get) {
                                                    $base = (string) ($get('id') ?: $record?->id);
                                                    $name = $base . '.' . ($file->getClientOriginalExtension() ?: 'pdf');
                                                    $dir  = 'reinsurers/OperativeDocuments';
                                                    Storage::disk('s3')->putFileAs($dir, $file, $name, ['visibility' => 'public']);
                                                    return "{$dir}/{$name}"; // <- esto se guarda en document_path
                                                })
                                                
                                                // 2) Borrado físico cuando Filament elimina el archivo subido
                                                ->deleteUploadedFileUsing(function (?string $file) {
                                                    if ($file && Storage::disk('s3')->exists($file)) {
                                                        Storage::disk('s3')->delete($file);
                                                    }
                                                })

                                                // 3) Si el usuario hace "clear" (icono de bote), borra en S3 y fuerza que BD quede en NULL
                                                ->afterStateUpdated(function ($state, \Filament\Forms\Set $set, \Filament\Forms\Get $get, $record) {
                                                    // Cuando se limpia el campo, $state viene como null/''.
                                                    if (blank($state) && $record?->document_path) {
                                                        if (Storage::disk('s3')->exists($record->document_path)) {
                                                            Storage::disk('s3')->delete($record->document_path);
                                                        }
                                                        // Asegura que el form state sea null para persistirlo
                                                        $set('document_path', null);
                                                    }
                                                })

                                                // 4) SIEMPRE deshidratar; y mutar '' -> null para que se escriba en BD
                                                ->dehydrated() // (sin callback) siempre escribe el estado
                                                ->mutateDehydratedStateUsing(fn ($state) => blank($state) ? null : $state)

                                                ->downloadable()
                                                ->openable()
                                                ->previewable(true)
                                                ->hint(fn ($record) => $record?->document_path
                                                    ? 'Existing file: ' . basename($record->document_path)
                                                    : 'No file uploaded yet.'
                                                )
                                                //->dehydrated(fn ($state) => filled($state))
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
                                        ->columnSpan(['md' => 12])
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

                                            ->required()
                                            ->minItems(1)
                                            ->defaultItems(1)

                                            ->schema([
                                                Select::make('cscheme_id')
                                                    ->label('Placement Scheme')
                                                    ->options(
                                                        \App\Models\CostScheme::query()
                                                            ->orderBy('index')
                                                            ->get()
                                                            ->mapWithKeys(function ($scheme) {
                                                                $shareFormatted = number_format($scheme->share * 100, 2) . '%';
                                                                return [
                                                                    $scheme->id => "{$scheme->id} · Index: {$scheme->index} · Share: {$shareFormatted} · Type: {$scheme->agreement_type}",
                                                                ];
                                                            })
                                                            ->all()
                                                    )
                                                    ->searchable()
                                                    ->preload()
                                                    ->reactive()
                                                    ->required(),

                                                Group::make()
                                                    ->schema([
                                                        View::make('partials.scheme-nodes-preview')
                                                            ->viewData(fn (Get $get) => [
                                                                'schemeId' => $get('cscheme_id'),
                                                            ])
                                                            ->columnSpan('full'),
                                                    ]),
                                            ])
                                            ->columns(1)
                                            ->addActionLabel('Add placement scheme')
                                            ->reorderable(false)

                                            /**
                                             * ✅ Caso BORRAR (basurero) dentro del repeater
                                             */
                                            ->deleteAction(fn (FormAction $action) => $action->after(function (Set $set, Get $get) {
                                                $allowed = collect($get('schemes') ?? [])
                                                    ->pluck('cscheme_id')
                                                    ->filter()
                                                    ->unique()
                                                    ->values()
                                                    ->all();

                                                $insureds = collect($get('insureds') ?? [])
                                                    ->map(function ($row) use ($allowed) {
                                                        if (! empty($row['cscheme_id']) && ! in_array($row['cscheme_id'], $allowed, true)) {
                                                            $row['cscheme_id'] = null;
                                                        }
                                                        return $row;
                                                    })
                                                    ->all();

                                                $set('insureds', $insureds);

                                                // 👇 fuerza refresco visual del select en insureds
                                                $set('schemes_version', (string) Str::uuid());
                                            }))

                                            /**
                                             * ✅ Caso CAMBIOS generales (add/remove/change select)
                                             */
                                            ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                                $allowed = collect($state ?? [])
                                                    ->pluck('cscheme_id')
                                                    ->filter()
                                                    ->unique()
                                                    ->values()
                                                    ->all();

                                                $insureds = collect($get('insureds') ?? [])
                                                    ->map(function ($row) use ($allowed) {
                                                        if (! empty($row['cscheme_id']) && ! in_array($row['cscheme_id'], $allowed, true)) {
                                                            $row['cscheme_id'] = null;
                                                        }
                                                        return $row;
                                                    })
                                                    ->all();

                                                $set('insureds', $insureds);

                                                // 👇 fuerza refresco visual del select en insureds
                                                $set('schemes_version', (string) Str::uuid());
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

                                                // 🟡 👇 AQUÍ VA LA LEYENDA (antes del Repeater)
                                                Placeholder::make('insureds_notice')
                                                    ->content(new HtmlString(
                                                        '
                                                        <div class="flex gap-2 items-start">
                                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                                class="h-5 w-5 mt-0.5 text-warning-600 dark:text-warning-400 flex-shrink-0"
                                                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                    d="M12 9v2m0 4h.01M10.29 3.86l-8.3 14.38A1 1 0 002.83 20h16.34a1 1 0 00.86-1.76L11.71 3.86a1 1 0 00-1.72 0z" />
                                                            </svg>

                                                            <div>
                                                                <strong>Important:</strong>
                                                                Please register the <em>Gross Reinsurance Premium</em>
                                                                <strong>per coverage and per insured company</strong>.
                                                                <br>
                                                                Placement scheme shares and totals are calculated automatically.
                                                            </div>
                                                        </div>
                                                        '
                                                    ))
                                                    ->extraAttributes([
                                                        'class' => '
                                                            text-sm text-gray-600 dark:text-gray-400
                                                            bg-warning-50 dark:bg-gray-800/40
                                                            border border-warning-100/40 dark:border-gray-700
                                                            rounded-md p-3
                                                        ',
                                                    ])
                                                    ->columnSpan(12),





                                                // 🟡 👇 Inicio del Repeater
                                                Repeater::make('insureds')
                                                    ->label('Insureds')
                                                    ->relationship()
                                                    ->required()
                                                    ->minItems(1)
                                                    ->defaultItems(1)
                                                    ->schema([
                                                        Select::make('company_id')
                                                            ->label('Company')
                                                            ->relationship('company', 'name')
                                                            ->preload()
                                                            ->optionsLimit(1000)
                                                            ->required()
                                                            ->searchable()
                                                            ->columnSpan(4),

                                                        Select::make('cscheme_id')
                                                            ->label('Placement scheme')
                                                            ->options(function (Get $get) {
                                                                $ids = collect($get('../../schemes') ?? [])
                                                                    ->pluck('cscheme_id')
                                                                    ->filter()
                                                                    ->unique()
                                                                    ->values();

                                                                return \App\Models\CostScheme::whereIn('id', $ids)
                                                                    ->get()
                                                                    ->mapWithKeys(fn ($s) => [
                                                                        $s->id => "{$s->id} · Index: {$s->index} · Share: " . number_format($s->share * 100, 2) . "%",
                                                                    ]);
                                                            })
                                                            ->searchable()
                                                            ->preload()
                                                            ->required()
                                                            ->live()
                                                            ->reactive()

                                                            // ✅ ESTA ES LA DIFERENCIA: forzar remount si cambian schemes
                                                            ->key(fn (Get $get) => 'insured-cscheme-' . ($get('../../schemes_version') ?? 'v0'))

                                                            // ✅ al cargar, si el valor ya no es válido, límpialo
                                                            ->afterStateHydrated(function (Set $set, Get $get, $state) {
                                                                $allowed = collect($get('../../schemes') ?? [])
                                                                    ->pluck('cscheme_id')
                                                                    ->filter()
                                                                    ->unique()
                                                                    ->values()
                                                                    ->all();

                                                                if (! empty($state) && ! in_array($state, $allowed, true)) {
                                                                    $set('cscheme_id', null);
                                                                }
                                                            })
                                                            ->columnSpan(3),
                                                            

                                                        Select::make('coverage_id')
                                                            ->label('Coverage')
                                                            ->options(function ($livewire) {
                                                                $business = method_exists($livewire, 'getOwnerRecord')
                                                                    ? $livewire->getOwnerRecord()
                                                                    : null;

                                                                if (! $business) return [];

                                                                return $business->liabilityStructures()
                                                                    ->with('coverage:id,name')
                                                                    ->get()
                                                                    ->pluck('coverage.name', 'coverage.id')
                                                                    ->filter()
                                                                    ->unique()
                                                                    ->toArray();
                                                            })

                                                            // ✅ Para que, si el valor ya existe pero ya no está en options(),
                                                            // el select muestre un texto humano y no el id.
                                                            ->getOptionLabelUsing(function ($value): ?string {
                                                                if (blank($value)) return null;

                                                                return Coverage::query()->whereKey($value)->value('name')
                                                                    ?? "Coverage #{$value} (removed from Liability Structures)";
                                                            })

                                                            // ✅ Helper dinámico que NO se rompe por tipos (string/int)
                                                            ->helperText(function (Get $get, $livewire): string {
                                                                $default = 'Select a coverage defined in the business Liability Structures.';
                                                                $coverageId = $get('coverage_id');

                                                                if (blank($coverageId)) return $default;

                                                                $business = method_exists($livewire, 'getOwnerRecord')
                                                                    ? $livewire->getOwnerRecord()
                                                                    : null;

                                                                if (! $business) return $default;

                                                                // Normaliza TODO a string para comparación estricta confiable
                                                                $coverageId = (string) $coverageId;

                                                                $validIds = $business->liabilityStructures()
                                                                    ->pluck('coverage_id')
                                                                    ->filter()
                                                                    ->unique()
                                                                    ->map(fn ($id) => (string) $id)
                                                                    ->values()
                                                                    ->all();

                                                                if (! in_array($coverageId, $validIds, true)) {
                                                                    return 'This coverage was removed from Liability Structures. Please select a valid coverage.';
                                                                }

                                                                return $default;
                                                            })

                                                            // ✅ Importante dentro de repeater para que recalculen closures al cambiar
                                                            ->reactive()
                                                            ->live()

                                                            ->searchable()
                                                            ->required()
                                                            ->columnSpan(3),


                                                        TextInput::make('premium')
                                                            ->label('Premium')
                                                            ->prefix('$')
                                                            ->type('text')
                                                            ->inputMode('decimal')
                                                            ->live(onBlur: true)
                                                            ->mask(RawJs::make('$money($input)'))
                                                            ->dehydrateStateUsing(fn ($state) => $state !== null
                                                                ? (float) str_replace([',', '$', ' '], '', $state)
                                                                : null
                                                            )
                                                            ->step(0.01)
                                                            ->required()
                                                            ->columnSpan(2),
                                                    ])
                                                    ->reorderableWithButtons()
                                                    ->defaultItems(1)
                                                    ->columns(12)
                                                    ->addActionLabel('Add Insured')
                                                    ->columnSpan(12)
                                                    ->live()
                                                    //Calculo del valor Total Gross Reinsurance Premium
                                                    ->afterStateUpdated(function (array $state, Set $set) {
                                                        // 1) scheme ids usados en insureds
                                                        $schemeIds = collect($state)
                                                            ->pluck('cscheme_id')
                                                            ->filter()
                                                            ->unique()
                                                            ->values()
                                                            ->all();

                                                        // 2) Mapa schemeId => share (ej. 0.70 / 1.00 / 0.30)
                                                        $shareByScheme = \App\Models\CostScheme::query()
                                                            ->whereIn('id', $schemeIds)
                                                            ->pluck('share', 'id')
                                                            ->map(fn ($v) => (float) $v)
                                                            ->toArray();

                                                        // 3) Total ponderado Σ(premium * share)
                                                        $weightedTotal = collect($state)
                                                            ->map(function ($row) use ($shareByScheme) {
                                                                $premiumRaw = $row['premium'] ?? 0;
                                                                $premium = (float) str_replace([',', '$', ' '], '', (string) $premiumRaw);

                                                                $schemeId = $row['cscheme_id'] ?? null;
                                                                $share = (float) ($shareByScheme[$schemeId] ?? 0);

                                                                return $premium * $share;
                                                            })
                                                            ->sum();

                                                        // 4) # de compañías únicas (company_id)
                                                        $companiesCount = collect($state)
                                                            ->pluck('company_id')
                                                            ->filter()
                                                            ->unique()
                                                            ->count();

                                                        // 5) Divide entre compañías únicas (evita división entre 0)
                                                        $final = $companiesCount > 0
                                                            ? ($weightedTotal / $companiesCount)
                                                            : 0;

                                                        $set('insureds_total', number_format($final, 2, '.', ','));
                                                    }),

                                                    /* ->afterStateUpdated(function ($state, callable $set) {
                                                        $total = collect($state)
                                                            ->pluck('premium')
                                                            ->filter()
                                                            ->map(fn ($value) => floatval(str_replace(',', '', $value)))
                                                            ->sum();

                                                        $set('insureds_total', number_format($total, 2, '.', ','));
                                                    }), */

                                                Placeholder::make('')->columnSpan(9),

                                                /* TextInput::make('insureds_total')
                                                    ->label(new HtmlString(
                                                        'Grand Total<br><span class="text-sm font-normal text-gray-500 dark:text-gray-400">
                                                        Gross Reinsurance Premium
                                                        </span>'
                                                    ))
                                                    ->prefix('$')
                                                    ->disabled()
                                                    ->dehydrated(false)
                                                    ->columnSpan(3), */
                                            ]),
                                    ]),  
                                //--- End Tab ----------------------------------------          
                           
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
                /* Section::make('Overview')
                    ->collapsible()
                    ->collapsed(fn (Get $get) => $get('active_panel') !== 'summary')
                    ->extraAttributes([
                        'x-on:click.self' => '$wire.set("data.active_panel","summary"); $wire.set("active_panel","summary");',
                        'class' => 'max-h-[700px] overflow-y-auto',
                    ])

                    // ⬇️ Botón para exportar/preview/imprimir
                    ->headerActions([
                        FormAction::make('Export to pdf'),
                    ]) */

                    Section::make()
                    ->schema([
                        Placeholder::make('')
                            ->content(new HtmlString('
                                <div class="space-y-2">
                                    <h2 class="text-base font-semibold text-gray-900 dark:text-gray-100">
                                        Warning
                                    </h2>

                                    <div class="
                                        flex items-center gap-2
                                        text-sm rounded-md p-3 border
                                        bg-warning-50 border-gray-200
                                        dark:bg-warning-900/20 dark:border-white/10
                                    ">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-danger-600 dark:text-danger-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 9v2m0 4h.01M10.29 3.86l-8.3 14.38A1 1 0 002.83 20h16.34a1 1 0 00.86-1.76L11.71 3.86a1 1 0 00-1.72 0z" />
                                        </svg>

                                        <span class="font-bold text-danger-700 dark:text-danger-300">
                                            Please switch back to &quot;Document Details&quot; to continue.
                                        </span>
                                    </div>
                                </div>
                            '))
                            ->extraAttributes([
                                'class' => '!p-0 !border-0 !bg-transparent',
                            ]),
                    ])
                    ->visible(fn (Get $get) => ($get('active_panel') ?? 'tabs') === 'summary')
                    ->hiddenOn('view'),
                        

                    Section::make('Overview')
                        ->visible(fn (Get $get) => ($get('active_panel') ?? 'tabs') === 'summary')
                        ->headerActions([
                            HeaderAction::make('goTabs')
                                ->label('Document Details')
                                ->icon('heroicon-o-document-text')
                                ->action(fn (Set $set) => $set('active_panel', 'tabs')),

                            
                        ])

                    ->schema([
                        View::make('filament.resources.business.operative-doc-summary_v1')
                            ->extraAttributes([
                                'class' => 'bg-[#dfe0e2] text-black p-4 rounded-md'
                            ])
                            ->reactive()
                            ->reactive()
                            ->viewData(function ($get, $record, $livewire) {
                                $business = method_exists($livewire, 'getOwnerRecord') ? $livewire->getOwnerRecord() : null;

                                /*================================================================
                                |  SCHEMES (Cost Schemes seleccionados en el formulario)
                                =================================================================*/
                                $schemes = collect($get('schemes') ?? [])
                                    ->map(function ($scheme) {
                                        $id = $scheme['cscheme_id'] ?? null;

                                        $model = \App\Models\CostScheme::find($id);

                                        return $model ? [
                                            'cscheme_id'     => $id,               // 👈 IMPORTANTE
                                            'id'             => $model->id,        // SCHE-....
                                            'description'    => $model->description,  // ✅ MOD [PS-DESC-1] NEW
                                            'share'          => $model->share,
                                            'agreement_type' => $model->agreement_type,
                                        ] : null;
                                    })
                                    ->filter()
                                    ->values()
                                    ->toArray();

                                $totalShare = collect($schemes)->sum('share'); // 🔹 total calculado

                                // ✅ NEW: mapa cscheme_id => share (para usarlo por insured)
                                $schemeShareById = collect($schemes)
                                    ->mapWithKeys(fn ($s) => [
                                        ($s['cscheme_id'] ?? null) => (float) ($s['share'] ?? 0),
                                    ])
                                    ->filter();

                                /*================================================================
                                |  INSUREDS (con cscheme_id incluido)
                                =================================================================*/
                                $insureds = collect($get('insureds') ?? [])->map(function ($insured) {
                                    $company  = \App\Models\Company::with('country')->find($insured['company_id'] ?? null);
                                    $coverage = \App\Models\Coverage::find($insured['coverage_id'] ?? null);

                                    $raw = $insured['premium'] ?? 0;
                                    $clean = is_string($raw) ? preg_replace('/[^0-9.]/', '', $raw) : $raw;
                                    if (is_string($clean)) {
                                        $parts = explode('.', $clean, 3);
                                        $clean = isset($parts[1]) ? $parts[0] . '.' . $parts[1] : $parts[0];
                                    }
                                    $premium = floatval($clean);

                                    return [
                                        'cscheme_id' => $insured['cscheme_id'] ?? null, // 👈 IMPORTANTE

                                        'company' => $company
                                            ? [
                                                'name' => $company->name,
                                                'country' => ['name' => optional($company->country)->name],
                                            ]
                                            : ['name' => '-', 'country' => ['name' => '-']],

                                        'coverage' => $coverage ? ['name' => $coverage->name] : ['name' => '-'],
                                        'premium'  => $premium,
                                    ];
                                })->toArray();

                                /*================================================================
                                |  COST NODES (igual que lo tenías)
                                =================================================================*/
                                $costNodes = collect($get('schemes') ?? [])
                                    ->pluck('cscheme_id')
                                    ->filter()
                                    ->unique()
                                    ->values()
                                    ->map(fn ($schemeId) =>
                                        CostScheme::with([
                                            'costNodexes.partnerSource',
                                            'costNodexes.deduction',
                                        ])->find($schemeId)
                                    )
                                    ->filter()
                                    ->flatMap(function (CostScheme $scheme) {
                                        return $scheme->costNodexes->map(function (CostNodex $node) use ($scheme) {
                                            $node->scheme_share = (float) $scheme->share; // 👈 solo display
                                            return $node;
                                        });
                                    })
                                    ->values();

                                /*================================================================
                                |  CÁLCULOS GENERALES (FTP / FTS)
                                =================================================================*/
                                $inception   = $get('inception_date');
                                $expiration  = $get('expiration_date');

                                $start       = $inception ? \Carbon\Carbon::parse($inception) : null;
                                $end         = $expiration ? \Carbon\Carbon::parse($expiration) : null;

                                // ✅ MOD [LEAP-1]: define días del año (según el año del start)
                                $daysInYear   = $start && $start->isLeapYear() ? 366 : 365;
                                // ✅ MOD [LEAP-2]: calcula coverageDays normal
                                $coverageDays = ($start && $end) ? $start->diffInDays($end) : 0;
                                // ✅ MOD [LEAP-3]: regla anti-distorsión (tu caso 31/12/2011 -> 31/12/2012)
                                if ($start && $end && $start->isSameDay($end->copy()->subYear())) {
                                    $coverageDays = $daysInYear;
                                }

                                $totalPremium = collect($insureds)->sum('premium');

                                $insureds = collect($insureds)->map(function ($insured) use ($totalPremium, $coverageDays, $daysInYear, $schemeShareById) {
                                    $allocation = $totalPremium > 0 ? $insured['premium'] / $totalPremium : 0;

                                    $premiumFtp = ($daysInYear > 0)
                                        ? ($insured['premium'] / $daysInYear) * $coverageDays
                                        : 0;

                                    $insuredSchemeId = $insured['cscheme_id'] ?? null;
                                    $share = (float) ($schemeShareById[$insuredSchemeId] ?? 0);

                                    $premiumFts = $premiumFtp * $share;

                                    return array_merge($insured, [
                                        'allocation_percent' => $allocation,
                                        'premium_ftp'        => $premiumFtp,
                                        'premium_fts'        => $premiumFts,
                                        'scheme_share'       => $share,
                                    ]);
                                })->toArray();

                                $totalPremiumFtp = ($daysInYear > 0) ? ($totalPremium / $daysInYear) * $coverageDays : 0;
                                $totalPremiumFts = collect($insureds)->sum('premium_fts');

                                /*================================================================
                                |  Converted Premium (igual, pero ahora usa el totalPremiumFts)
                                =================================================================*/
                                $transactions = collect($get('transactions') ?? []);
                                $totalConvertedPremium = 0;

                                foreach ($transactions as $txn) {
                                    $proportion = floatval($txn['proportion'] ?? 0) / 100;
                                    $rate = floatval($txn['exch_rate'] ?? 0);

                                    if ($rate > 0) {
                                        $totalConvertedPremium += ($totalPremiumFts * $proportion) / $rate;
                                    } else {
                                        $totalConvertedPremium = 1;
                                    }
                                }

                                /*================================================================
                                |  ✅✅✅ COSTS BREAKDOWN (FIX: NO duplicar share)
                                |  - Base por scheme = SUM(premium_fts) de insureds de ese scheme
                                |  - Deduction = base_scheme * node.value
                                |  - USD base por scheme usando installments
                                =================================================================*/

                                // ✅ MOD [CB-1]: base Orig. Curr por scheme (sum premium_fts del scheme)
                                $premiumFtsByScheme = collect($insureds) // ✅ MOD [CB-1]
                                    ->groupBy('cscheme_id')             // ✅ MOD [CB-1]
                                    ->map(fn ($rows) => $rows->sum('premium_fts')); // ✅ MOD [CB-1]

                                // ✅ MOD [CB-2]: base USD por scheme (aplicando installments)
                                $convertedFtsByScheme = $premiumFtsByScheme->map(function ($schemeFts) use ($transactions) { // ✅ MOD [CB-2]
                                    $converted = 0.0;                                                                   // ✅ MOD [CB-2]

                                    foreach ($transactions as $txn) {                                                    // ✅ MOD [CB-2]
                                        $proportion = floatval($txn['proportion'] ?? 0) / 100;                           // ✅ MOD [CB-2]
                                        $rate       = floatval($txn['exch_rate'] ?? 0);                                  // ✅ MOD [CB-2]

                                        if ($rate > 0) {                                                                 // ✅ MOD [CB-2]
                                            $converted += ($schemeFts * $proportion) / $rate;                            // ✅ MOD [CB-2]
                                        }                                                                                // ✅ MOD [CB-2]
                                    }                                                                                    // ✅ MOD [CB-2]

                                    return $converted;                                                                    // ✅ MOD [CB-2]
                                });                                                                                       // ✅ MOD [CB-2]

                                // ✅ MOD [CB-3]: recalcular groupedCostNodes con fórmula correcta
                                $totalDeductionOrig = 0;
                                $totalDeductionUsd  = 0;

                                $groupedCostNodes = $costNodes
                                    ->groupBy('cscheme_id')
                                    ->map(function ($nodes, $schemeId) use (
                                        &$totalDeductionOrig,
                                        &$totalDeductionUsd,
                                        $premiumFtsByScheme,
                                        $convertedFtsByScheme
                                    ) {
                                        /** @var \App\Models\CostNodex $first */
                                        $first      = $nodes->first();
                                        $shareFloat = (float) ($first->scheme_share ?? 0); // ✅ MOD [CB-3] solo display

                                        // ✅ MOD [CB-3]: base por scheme (orig y usd)
                                        $schemeBaseOrig = (float) ($premiumFtsByScheme[$schemeId] ?? 0);   // ✅ MOD [CB-3]
                                        $schemeBaseUsd  = (float) ($convertedFtsByScheme[$schemeId] ?? 0); // ✅ MOD [CB-3]

                                        $nodeList = $nodes->map(function (CostNodex $node) use ($schemeBaseOrig, $schemeBaseUsd, $shareFloat) {
                                            $rate = (float) ($node->value ?? 0); // ej 0.02

                                            // ✅ MOD [CB-3]: NO multiplica share otra vez
                                            $deductionOrig = $schemeBaseOrig * $rate; // ✅ MOD [CB-3]
                                            $deductionUsd  = $schemeBaseUsd  * $rate; // ✅ MOD [CB-3]

                                            return [
                                                'index'            => $node->index,
                                                'partner'          => $node->partnerSource?->name ?? '-',
                                                'partner_short'    => $node->partnerSource?->short_name
                                                                    ?? $node->partnerSource?->name
                                                                    ?? '-',
                                                'deduction'        => $node->deduction?->concept ?? '-',
                                                'value'            => $rate,
                                                'share'            => $shareFloat,       // solo display
                                                'scheme_base_orig' => $schemeBaseOrig,   // ✅ MOD [CB-3] (para fórmula)
                                                'scheme_base_usd'  => $schemeBaseUsd,    // ✅ MOD [CB-3] (para fórmula)
                                                'deduction_amount' => $deductionOrig,
                                                'deduction_usd'    => $deductionUsd,
                                            ];
                                        })->values();

                                        $subtotalOrig = $nodeList->sum('deduction_amount');
                                        $subtotalUsd  = $nodeList->sum('deduction_usd');

                                        $totalDeductionOrig += $subtotalOrig;
                                        $totalDeductionUsd  += $subtotalUsd;

                                        return [
                                            'scheme_id'        => $schemeId,
                                            'share'            => $shareFloat,
                                            'scheme_base_orig' => $schemeBaseOrig, // ✅ MOD [CB-3]
                                            'scheme_base_usd'  => $schemeBaseUsd,  // ✅ MOD [CB-3]
                                            'nodes'            => $nodeList,
                                            'subtotal_orig'    => $subtotalOrig,
                                            'subtotal_usd'     => $subtotalUsd,
                                        ];
                                    })
                                    ->values()
                                    ->toArray();

                                /*================================================================
                                |  LOGS (igual que lo tenías)
                                =================================================================*/

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

                                /*================================================================
                                |  RETURN A LA VISTA
                                =================================================================*/
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
                                    'totalConvertedPremium' => $totalConvertedPremium, // (global)

                                    // ✅ MOD [CB-RET]: opcional si quieres usarlo en la vista (fórmulas)
                                    'premiumFtsByScheme'     => $premiumFtsByScheme,     // ✅ MOD [CB-RET]
                                    'convertedFtsByScheme'   => $convertedFtsByScheme,   // ✅ MOD [CB-RET]

                                    'coverageDays' => $coverageDays,
                                    'totalDeductionOrig' => $totalDeductionOrig, // ✅ ya corresponde al breakdown nuevo
                                    'totalDeductionUsd' => $totalDeductionUsd,   // ✅ ya corresponde al breakdown nuevo
                                    'totalShare' => $totalShare,

                                    'transactions' => collect($get('transactions') ?? [])->values(),
                                    'logsByTxn' => $logsByTxn,
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
            TextColumn::make('index')
                ->sortable()
                ->verticalAlignment(VerticalAlignment::Start) 
                ->sortable()
                ->searchable(),

            TextColumn::make('id')
                ->label('Document code')
                ->sortable()
                ->verticalAlignment(VerticalAlignment::Start) 
                ->copyable()
                ->sortable()
                ->searchable()
                ->tooltip(fn ($state) => $state) 
                ->extraAttributes(['class' => 'w-64']), // 👈 Ajusta el ancho

            TextColumn::make('docType.name')
                ->label('Doc Type')
                ->sortable()
                ->verticalAlignment(VerticalAlignment::Start) 
                ->sortable()
                ->searchable(),

            TextColumn::make('description')
                ->searchable()
                ->sortable()
                ->verticalAlignment(VerticalAlignment::Start) 
                ->wrap() // 👈 permite múltiples líneas
                ->extraAttributes([
                        'style' => 'width: 250px; white-space: normal; vertical-align: top;',
                    ]),

            TextColumn::make('inception_date')
                ->sortable()
                ->verticalAlignment(VerticalAlignment::Start)   
                ->date(),

            TextColumn::make('expiration_date')
                ->sortable()
                ->verticalAlignment(VerticalAlignment::Start) 
                ->date(),
            
            TextColumn::make('status')
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

            IconColumn::make('document_path')
                ->label('File')
                ->sortable()
                ->verticalAlignment(VerticalAlignment::Start)
                ->getStateUsing(fn ($record) => true)
                ->icon(fn ($record) =>
                    $record->document_path ? 'heroicon-o-document' : 'heroicon-o-x-circle'
                )
                ->color(fn ($record) => $record->document_path ? 'primary' : 'danger')
                ->tooltip(fn ($record) =>
                    $record->document_path ? 'View PDF' : 'No document available'
                )
                ->action(
                    Action::make('viewPdf')
                        ->label('View PDF')
                        ->icon('heroicon-o-document-text')
                        ->hidden(fn ($record) => blank($record->document_path))
                        ->modalHeading(fn ($record) => "PDF – {$record->id}")
                        ->modalWidth('7xl')
                        ->modalSubmitAction(false)
                        ->modalContent(function ($record) {
                            if (blank($record->document_path)) {
                                return new HtmlString('<p>No document available.</p>');
                            }

                            // Usa la ruta interna
                            $url = route('pdf.viewer', [
                                'operativeDoc' => $record->getKey(),
                            ]);

                            return view('filament.components.pdf-viewer', [
                                'url' => $url,
                            ]);
                        })
                ),
            /* Tables\Columns\IconColumn::make('document_path')
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

                    /** @var \Illuminate\Filesystem\FilesystemAdapter $s3
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
                ), */

        ])


        ->filters([
            //
        ])
        ->headerActions([
            Tables\Actions\CreateAction::make()
                ->label('➕ New Operative Doc')
                ->modalHeading('➕ Create Operative Doc')
                ->modalWidth('7xl')

                ->createAnother(false)                 // 👈 oculta "Create & create another"
                ->modalSubmitActionLabel('Create')     // 👈 botón principal
                ->modalCancelActionLabel('Cancel')     // 👈 botón cancelar

                // ✅ CAMBIO 3A-1: Deshabilita el botón CREATE cuando estás en Overview (summary)
                ->modalSubmitAction(fn (\Filament\Actions\StaticAction $action) => $action
                    ->disabled(fn () => $this->isOverviewActive())
                    ->tooltip(fn () => $this->isOverviewActive()
                        ? 'Go back to "Document Details" to continue.'
                        : null
                    )
                )

                // ✅ CAMBIO 3A-2: Deshabilita el botón CANCEL cuando estás en Overview (summary)
                ->modalCancelAction(fn (\Filament\Actions\StaticAction $action) => $action
                    ->disabled(fn () => $this->isOverviewActive())
                    ->tooltip(fn () => $this->isOverviewActive()
                        ? 'Go back to "Document Details" to continue.'
                        : null
                    )
                )

                // ✅ CAMBIO 3A-3: tu "seguro" en before, pero SIN $livewire
                ->before(function (TableAction $action) {
                    if ($this->isOverviewActive()) {
                        Notification::make()
                            ->title('Overview mode')
                            ->body('Go back to "Document Details" before creating.')
                            ->warning()
                            ->send();

                        $action->halt();
                    }
                })


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
                    $business = $livewire->ownerRecord;

                    // ✅ tu lógica actual de generar ID (la dejas igual)
                    if (! isset($data['id'])) {
                        $lastIndex = $business->operativeDocs()
                            ->withTrashed()
                            ->get()
                            ->map(fn ($doc) => intval(substr($doc->id, -2)))
                            ->max();

                        $newIndex = $lastIndex ? $lastIndex + 1 : 1;
                        $data['id'] = $business->business_code . '-' . str_pad($newIndex, 2, '0', STR_PAD_LEFT);
                    }

                    // ✅ CAMBIO: si es el primer documento, fuerza Slip = 1
                    $slipExists = $business->operativeDocs()
                        ->where('operative_doc_type_id', 1)
                        ->exists();

                    if (! $slipExists && empty($data['operative_doc_type_id'])) {
                        $data['operative_doc_type_id'] = 1;
                    }

                    return $data;
                })
                
        ])


        ->actions([
            Tables\Actions\ActionGroup::make([

                Tables\Actions\ViewAction::make('view')
                    ->label('View')
                    //->color('primary')
                    ->modalHeading(fn ($record) => '📄 Reviewing ' . $record->docType->name .' — '. $record->id )
                    ->modalWidth('7xl'),  

                
                Tables\Actions\EditAction::make('edit')
                    ->label('Edit')
                    //->color('primary')
                    ->modalHeading(fn ($record) => '📝 Modifying ' . $record->docType->name .' — '. $record->id )
                    ->modalWidth('7xl')

                    // ✅ CAMBIO 3B-1: Deshabilita el botón SAVE CHANGES cuando estás en Overview (summary)
                    ->modalSubmitAction(fn (\Filament\Actions\StaticAction $action) => $action
                        ->disabled(fn () => $this->isOverviewActive())
                        ->tooltip(fn () => $this->isOverviewActive()
                            ? 'Go back to "Document Details" to continue.'
                            : null
                        )
                    )

                    // ✅ CAMBIO 3B-2: Deshabilita el botón CANCEL cuando estás en Overview (summary)
                    ->modalCancelAction(fn (\Filament\Actions\StaticAction $action) => $action
                        ->disabled(fn () => $this->isOverviewActive())
                        ->tooltip(fn () => $this->isOverviewActive()
                            ? 'Go back to "Document Details" to continue.'
                            : null
                        )
                    )

                    // ✅ CAMBIO 3B-2: tu "seguro" en before, pero SIN $livewire
                    ->before(function (TableAction $action) {
                        if ($this->isOverviewActive()) {
                            Notification::make()
                                ->title('Overview mode')
                                ->body('Go back to "Document Details" before saving.')
                                ->warning()
                                ->send();

                            $action->halt();
                        }
                    })

                    ->mutateFormDataUsing(function (array $data, $livewire, $record) {

                        // ✅ CAMBIO A4: si el record es Slip, no permitas cambiar el tipo
                        if ((int) ($record->operative_doc_type_id ?? 0) === 1) {
                            $data['operative_doc_type_id'] = 1; // fuerza Slip
                        }

                        return $data;
                    }),

                // ──────  PRINT SUMMARY  ─────────────────────────
                Action::make('printSummaryV2')
                    ->label('Summary')
                    ->icon('heroicon-o-printer')
                    //->color('primary')
                    ->outlined()

                    ->disabled(function (): bool {
                        /** @var \App\Models\User|null $user */
                        $user = Filament::auth()->user();

                        return ! ($user?->can('print_summary_business') ?? false);
                    })
                    ->tooltip(function (): ?string {
                        /** @var \App\Models\User|null $user */
                        $user = Filament::auth()->user();

                        return ($user?->can('print_summary_business') ?? false)
                            ? 'Open printable summary'
                            : 'You do not have permission to print the summary.';
                    })
                    ->modalHeading(fn ($record) => "🖨️ Summary — {$record->id}")
                    ->modalWidth('7xl')
                    ->modalSubmitAction(false) // quita el botón "Submit" default
                    ->modalCancelActionLabel('Close')

                    ->modalContent(function ($record) {
                        $user = Filament::auth()->user();
                        /** @var \App\Models\User|null $user */
                        if (! ($user?->can('print_summary_business') ?? false)) {
                            return new HtmlString('<div class="p-4 text-sm text-gray-500">Permission denied.</div>');
                        }

                        $data = app(OperativeDocSummaryV2Service::class)->build($record->id);

                        return view('filament.resources.business.operative-doc-summary_v2', $data);
                    })
                    
                    ->modalFooterActions([
                        Action::make('print')
                            ->label('Print')
                            ->icon('heroicon-o-printer')
                            ->color('primary')
                            ->extraAttributes([
                                'type' => 'button',
                                'class' => 'no-print', // 👈 AQUÍ
                            ])
                            ->alpineClickHandler(function ($record) {
                                $date = $record->creation_date ?? $record->created_at ?? now();

                                // 👉 31-Dec-2010
                                $formatted = Carbon::parse($date)->format('d-M-Y');

                                // 👉 Summary_2010-CMY001-001-01_(31-Dec-2010)
                                $filename = "Summary_{$record->id}_({$formatted})";
                                $jsFilename = json_encode($filename);

                                return <<<JS
                                    const oldTitle = document.title;
                                    document.title = {$jsFilename};

                                    const restore = () => {
                                        document.title = oldTitle;
                                        window.removeEventListener('afterprint', restore);
                                    };

                                    window.addEventListener('afterprint', restore);

                                    window.print();
                                JS;
                            }),
                    ]),







                // ──────  DIVIDER 1  ─────────────────────────
                Tables\Actions\Action::make('divider_1')
                    ->label('')
                    ->disabled()
                    ->extraAttributes([
                        'class' => 'pointer-events-none border-t border-gray-900 my-1',
                        'style' => 'height: 0; padding: 0; margin: 1px 0;',
                    ]),



                // ──────  ADD TRANSACTION  ─────────────────────────
                Action::make('addTransaction')
                    ->label('Add transaction')
                    ->color('primary')
                    ->outlined()
                    ->icon('heroicon-o-plus-circle')
                    ->disabled(function (): bool {
                        /** @var \App\Models\User|null $user */
                        $user = Filament::auth()->user();

                        return ! ($user?->can('business.add_transaction') ?? false);
                    })
                    ->tooltip(function (): ?string {
                        /** @var \App\Models\User|null $user */
                        $user = Filament::auth()->user();

                        return ($user?->can('business.add_transaction') ?? false)
                            ? 'Add a transaction to this operative document'
                            : 'You do not have permission to add transactions.';
                    })
                    ->action(function ($record): void {
                        /** @var \App\Models\User|null $user */
                        $user = Filament::auth()->user();

                        if (! ($user?->can('business.add_transaction') ?? false)) {
                            Notification::make()
                                ->title('Permission denied')
                                ->body('You do not have permission to add transactions.')
                                ->danger()
                                ->send();

                            return;
                        }

                        redirect()->to(
                            \App\Filament\Resources\TransactionResource::getUrl('create', [
                                'op_document_id' => $record->id,
                            ])
                        );
                    }),


                // ──────  GO TO TRANSACTIONs  ─────────────────────────
                Action::make('viewTransactions')
                    ->label('View transactions')
                    ->color('primary')
                    ->outlined()
                    ->icon('heroicon-o-queue-list')
                    ->disabled(function (): bool {
                        /** @var \App\Models\User|null $user */
                        $user = Filament::auth()->user();

                        return ! ($user?->can('business.view_transactions') ?? false);
                    })
                    ->tooltip(function (): ?string {
                        /** @var \App\Models\User|null $user */
                        $user = Filament::auth()->user();

                        return ($user?->can('business.view_transactions') ?? false)
                            ? 'Open the transactions list filtered by this document'
                            : 'You do not have permission to view transactions.';
                    })
                    ->action(function ($record): void {
                        /** @var \App\Models\User|null $user */
                        $user = Filament::auth()->user();

                        if (! ($user?->can('business.view_transactions') ?? false)) {
                            Notification::make()
                                ->title('Permission denied')
                                ->body('You do not have permission to view transactions.')
                                ->danger()
                                ->send();

                            return;
                        }

                        // ✅ AJUSTA: relación/consulta para validar si tiene transacciones
                        // Opción A (si tienes relación $record->transactions()):
                        // $hasTx = $record->transactions()->exists();

                        // Opción B (si NO tienes relación y tu FK en transactions es op_document_id):
                        $hasTx = \App\Models\Transaction::query()
                            ->where('op_document_id', $record->id) // ✅ AJUSTA nombre FK
                            ->exists();

                        if (! $hasTx) {
                            Notification::make()
                                ->title('No transactions')
                                ->body('This operative document has no transactions yet.')
                                ->warning()
                                ->send();

                            return;
                        }

                        // ✅ Redirige al listado con filtro aplicado (Filament v3)
                        redirect()->to(
                            \App\Filament\Resources\TransactionResource::getUrl('index', [
                                'tableFilters' => [
                                    'op_document_id' => [
                                        'value' => $record->id,
                                    ],
                                ],
                            ])
                        );
                    }),




                // ──────  DIVIDER 2  ─────────────────────────
                Tables\Actions\Action::make('divider_1')
                        ->label('')
                        ->disabled()
                        ->extraAttributes([
                            'class' => 'pointer-events-none border-t border-gray-900 my-1',
                            'style' => 'height: 0; padding: 0; margin: 1px 0;',
                        ]),


                Tables\Actions\DeleteAction::make(),
        ]),
    ])    
        
        
        
        
        ->bulkActions([
            //Tables\Actions\DeleteBulkAction::make(),
        ]);
    }
}
