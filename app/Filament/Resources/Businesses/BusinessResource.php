<?php

namespace App\Filament\Resources\Businesses;

//use App\Filament\Resources\Businesses\BusinessResource;
use Filament\Forms\Form;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Actions\ActionGroup;
use Filament\Actions\ViewAction;
use Filament\Actions\EditAction;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use App\Filament\Resources\Businesses\RelationManagers\LiabilityStructuresRelationManager;
use App\Filament\Resources\Businesses\RelationManagers\OperativeDocsRelationManager;
use App\Filament\Resources\Businesses\Pages\ListBusinesses;
use App\Filament\Resources\Businesses\Pages\CreateBusiness;
use App\Filament\Resources\Businesses\Pages\EditBusiness;
use App\Filament\Resources\Businesses\Pages\ViewBusiness;
use App\Filament\Resources\Businesses\Widgets\BusinessStatsOverview;
use App\Filament\Resources\BusinessResource\Pages;
use App\Filament\Resources\BusinessResource\RelationManagers;
use App\Models\Business;
use App\Models\Reinsurer;
use Filament\Schemas\Components\Group;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Illuminate\Support\Str;
use Filament\Forms\Components\DatePicker;
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
use Filament\Infolists\Components\ViewEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Group as ComponentsGroup;

class BusinessResource extends Resource
{
    protected static ?string $model = Business::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-minus';
    protected static string | \UnitEnum | null $navigationGroup = 'Underwritten';
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
                'user:id,name',
            ])
            ->withCount([
                'operativeDocs',
            ]);
    }


    /* =========================
     *  FORM  (create / edit)
     * ========================= */
    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([

                Section::make()
                    ->columnSpan('full')
                    ->schema([

                        Grid::make(12)
                            ->extraAttributes([
                                'class' => 'w-full items-start gap-6',
                            ])
                            
                            ->schema([

                                /*
                                |--------------------------------------------------------------------------
                                | GENERAL DETAILS
                                |--------------------------------------------------------------------------
                                */

                                Section::make('General Details')
                                    ->columnSpan(8)
                                    ->columns(6)
                                    ->schema([

                                        Select::make('reinsurer_id')
                                            ->label('Reinsurer')
                                            ->options(fn () => \App\Models\Reinsurer::query()
                                                ->orderBy('name')
                                                ->get()
                                                ->mapWithKeys(fn ($reinsurer) => [
                                                    $reinsurer->id =>
                                                        "{$reinsurer->short_name} - {$reinsurer->name}"
                                                ]))
                                            ->searchable()
                                            ->preload()
                                            ->native(false)
                                            ->required()
                                            ->columnSpan(2),

                                        TextInput::make('index')
                                            ->numeric()
                                            ->required()
                                            ->default(fn () =>
                                                (\App\Models\Business::max('index') ?? 0) + 1
                                            )
                                            ->disabledOn(['create', 'edit'])
                                            ->dehydrated()
                                            ->columnSpan(2),

                                        TextInput::make('business_code')
                                            ->label('Business Code')
                                            ->disabled()
                                            ->dehydrated()
                                            ->required()
                                            ->unique(ignoreRecord: true)
                                            ->hiddenOn('create')
                                            ->columnSpan(2),

                                        Textarea::make('description')
                                            ->rows(5)
                                            ->required()
                                            ->columnSpanFull(),

                                        Select::make('business_type')
                                            ->options([
                                                'Own' => 'Own',
                                                'Third party' => 'Third party',
                                            ])
                                            ->searchable()
                                            ->required()
                                            ->columnSpan(3),

                                        Select::make('purpose')
                                            ->options([
                                                'Traditional' => 'Traditional',
                                                'Strategic' => 'Strategic',
                                            ])
                                            ->searchable()
                                            ->required()
                                            ->columnSpan(3),

                                        Select::make('parent_id')
                                            ->label('Treaty')
                                            ->relationship('treaty', 'treaty_code')
                                            ->searchable()
                                            ->columnSpan(3)
                                            ->preload(),

                                        Select::make('renewed_from_id')
                                            ->label('Renewed From')
                                            ->relationship('renewedFrom', 'business_code')
                                            ->searchable()
                                            ->columnSpan(3)
                                            ->preload(),

                                        TextInput::make('source_code')
                                            ->label('Original ID')
                                            ->placeholder('Enter original id if necessary.')
                                            ->columnSpan(3),
                                    ]),

                                /*
                                |--------------------------------------------------------------------------
                                | CONTRACT ATTRIBUTES
                                |--------------------------------------------------------------------------
                                */

                                Section::make('Contract Attributes')
                                    ->columnSpan(4)
                                    ->schema([

                                        Select::make('reinsurance_type')
                                            ->label('Contract Type')
                                            ->options([
                                                'Facultative' => 'Facultative',
                                                'Treaty' => 'Treaty',
                                            ])
                                            ->default('Facultative')
                                            ->searchable()
                                            ->required(),

                                        Select::make('risk_covered')
                                            ->label('Risk Covered')
                                            ->options([
                                                'Life' => 'Life',
                                                'Non-Life' => 'Non-Life',
                                            ])
                                            ->default('Non-Life')
                                            ->searchable()
                                            ->required(),

                                        Select::make('premium_type')
                                            ->label('Premium Type')
                                            ->options([
                                                'Fixed' => 'Fixed',
                                                'Estimated' => 'Estimated',
                                                'Declared' => 'Declared',
                                            ])
                                            ->default('Fixed')
                                            ->searchable()
                                            ->required(),

                                        Select::make('claims_type')
                                            ->label('Claims Type')
                                            ->options([
                                                'Claims occurrence' => 'Claims occurrence',
                                                'Claims made' => 'Claims made',
                                                'Hybrid' => 'Hybrid',
                                            ])
                                            ->searchable()
                                            ->required(),

                                        Select::make('currency_id')
                                            ->label('Currency')
                                            ->relationship(
                                                name: 'currency',
                                                titleAttribute: 'name',
                                                modifyQueryUsing: fn (Builder $query) =>
                                                    $query->orderBy('acronym')
                                            )
                                            ->getOptionLabelFromRecordUsing(
                                                fn ($record) =>
                                                    "{$record->acronym} - {$record->name}"
                                            )
                                            ->searchable(['name', 'acronym'])
                                            ->preload()
                                            ->default(157)
                                            ->required(),

                                        Select::make('region_id')
                                            ->label('Region')
                                            ->relationship('region', 'name')
                                            ->searchable()
                                            ->preload()
                                            ->default(2)
                                            ->required(),

                                        Select::make('producer_id')
                                            ->label('Producer')
                                            ->relationship('producer', 'name')
                                            ->searchable()
                                            ->preload()
                                            ->default(96)
                                            ->required(),
                                    ]),
                            ]),

                        /*
                        |--------------------------------------------------------------------------
                        | STATUS TRACKING
                        |--------------------------------------------------------------------------
                        */

                        Section::make('Status Tracking')
                            ->columns(3)
                            ->hiddenOn(['create', 'edit'])
                            ->schema([

                                Select::make('business_lifecycle_status')
                                    ->label('Lifecycle Status')
                                    ->options([
                                        'On Hold' => 'On Hold',
                                        'In Force' => 'In Force',
                                        'To Expire' => 'To Expire',
                                        'Expired' => 'Expired',
                                        'Cancelled' => 'Cancelled',
                                    ])
                                    ->default('On Hold')
                                    ->native(false)
                                    ->searchable()
                                    ->disabled(),

                                TextInput::make('approval_status')
                                    ->disabled()
                                    ->default('DFT'),

                                DatePicker::make('approval_status_updated_at')
                                    ->label('Approval Date')
                                    ->disabled(),
                            ]),
                    ]),
            ]);
    }










    /* =========================
     *  INFOLIST  (VIEW PAGE)
     * ========================= */
    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([

            Section::make() // o InfoSection::make('Business Details')
            ->columnSpan('full')
            ->schema([

                /* ─────────────────────────  BUSINESS IDENTITY  ───────────────────────── */
                Section::make('General Details')
                    ->compact()
                    ->schema([
                        \Filament\Schemas\Components\Grid::make(12)->schema([

                            // ✅ Columna izquierda (8)
                            Section::make()
                                ->compact()
                                ->schema([



                                    Section::make()
                                        ->compact()
                                        ->schema([
                                            \Filament\Schemas\Components\Grid::make(12)
                                            ->extraAttributes(['style' => 'gap:1px;padding:1px 0;'])
                                                ->schema([

                                                TextEntry::make('underwritten_by')
                                                    ->hiddenLabel()
                                                    ->state(function ($record) {
                                                        $name = $record->reinsurer?->name ?? '—';

                                                        return new HtmlString(
                                                            "<strong>Underwritten by:</strong> {$name}"
                                                        );
                                                    })
                                                    ->columnSpan(7),

                                                TextEntry::make('business_code_entry')
                                                    ->hiddenLabel()
                                                    ->state(function ($record) {
                                                        $code = $record->business_code ?: '—';

                                                        return new HtmlString(
                                                            "<strong>Business code:</strong> {$code}"
                                                        );
                                                    })
                                                    ->columnSpan(5),


                                            ]),

                                        ]),

                                    // Description
                                    Section::make('Description')
                                        ->compact()
                                        ->schema([
                                            \Filament\Schemas\Components\Grid::make(12)
                                                ->extraAttributes(['style' => 'gap:1px;padding:1px 0;'])
                                                ->schema([
                                                    TextEntry::make('gd_desc_value')
                                                        ->hiddenLabel()
                                                        ->state(fn ($record) => $record->description ?: '—')
                                                        ->extraAttributes(['style' => 'line-height:1;'])
                                                        ->columnSpan(12),
                                                ]),
                                        ]),

                                    Section::make()
                                        ->compact()
                                        ->schema([
                                            \Filament\Schemas\Components\Grid::make(12)
                                            ->extraAttributes(['style' => 'gap:1px;padding:1px 0;'])
                                                ->schema([

                                                    TextEntry::make('business_type_entry')
                                                        ->hiddenLabel()
                                                        ->state(function ($record) {
                                                            $value = $record->business_type ?: '—';

                                                            return new HtmlString(
                                                                "<strong>Business type:</strong> {$value}"
                                                            );
                                                        })
                                                        ->columnSpan(4),

                                                    TextEntry::make('purpose_entry')
                                                        ->hiddenLabel()
                                                        ->state(function ($record) {
                                                            $value = $record->purpose ?: '—';

                                                            return new HtmlString(
                                                                "<strong>Purpose:</strong> {$value}"
                                                            );
                                                        })
                                                        ->columnSpan(4),


                                                ]),
                                        ]),

                                    Section::make()
                                        ->compact()
                                        ->schema([
                                            \Filament\Schemas\Components\Grid::make(12)
                                            ->extraAttributes(['style' => 'gap:1px;padding:1px 0;'])
                                                ->schema([

                                                    TextEntry::make('parent_treaty_entry')
                                                        ->hiddenLabel()
                                                        ->state(function ($record) {
                                                            $value = $record->parent?->treaty_code ?: '—';

                                                            return new HtmlString(
                                                                "<strong>Parent treaty:</strong> {$value}"
                                                            );
                                                        })
                                                        ->columnSpan(4),

                                                    TextEntry::make('renewed_from_entry')
                                                        ->hiddenLabel()
                                                        ->state(function ($record) {
                                                            $value = $record->renewedFrom?->business_code ?: '—';

                                                            return new HtmlString(
                                                                "<strong>Renewed from:</strong> {$value}"
                                                            );
                                                        })
                                                        ->columnSpan(4),


                                                ]),
                                        ]),


                                    Section::make()
                                        ->compact()
                                        ->schema([
                                            \Filament\Schemas\Components\Grid::make(12)
                                            ->extraAttributes(['style' => 'gap:1px;padding:1px 0;'])
                                                ->schema([

                                                    TextEntry::make('source_code')
                                                        ->hiddenLabel()
                                                        ->state(function ($record) {
                                                            $value = $record->source_code ?: '—';

                                                            return new HtmlString(
                                                                "<strong>Source Id:</strong> {$value}"
                                                            );
                                                        })
                                                        ->columnSpan(4),



                                                ]),
                                        ]),    

                    ])
                    ->columnSpan(8),




                    /* ─────────────────────────  CONTRACT TERMS  ───────────────────────── */
                    Section::make('Contract Attributes') // cambia el título si quieres
                        ->compact()
                        ->schema([
                            

                                TextEntry::make('reinsurance_type_entry')
                                    ->hiddenLabel()
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
                                    ->hiddenLabel()
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
                                    ->hiddenLabel()
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
                                    ->hiddenLabel()
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
                                    ->hiddenLabel()
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
                                    ->hiddenLabel()
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
                                    ->hiddenLabel()
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
                Section::make('Lifecycle')
                    ->schema([
                        // 3 pares por fila → 12 cols / 4 = 3 columnas
                        \Filament\Schemas\Components\Grid::make(12)->schema([

        
                           TextEntry::make('approval_status_entry')
                                ->hiddenLabel()
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
                                ->hiddenLabel()
                                ->state(function ($record) {
                                    $value = $record->approval_status_updated_at?->format('Y-m-d') ?: '—';

                                    return new HtmlString(
                                        "<strong>Approval date:</strong> {$value}"
                                    );
                                })
                                ->columnSpan(2),

                            TextEntry::make('lifecycle_status_entry')
                                ->hiddenLabel()
                                ->state(function ($record) {
                                    $status = $record->business_lifecycle_status;
                                    $value  = $status?->value ?? null;

                                    if (! $value) {
                                        return new HtmlString('<strong>Lifecycle status:</strong> —');
                                    }

                                    [$bg, $text] = match ($value) {
                                        'In Force'  => ['light-dark(#dcfce7,#14532d)', 'light-dark(#166534,#86efac)'],
                                        'To Expire' => ['light-dark(#fef9c3,#713f12)', 'light-dark(#854d0e,#fde047)'],
                                        'Expired'   => ['light-dark(#fee2e2,#7f1d1d)', 'light-dark(#991b1b,#fca5a5)'],
                                        default     => ['light-dark(#f3f4f6,#374151)', 'light-dark(#374151,#d1d5db)'],
                                    };

                                    $badge = "<span style=\"display:inline-flex;align-items:center;padding:2px 10px;border-radius:9999px;font-size:14px;font-weight:500;background-color:{$bg};color:{$text}\">{$value}</span>";

                                    return new HtmlString("<strong>Lifecycle status:</strong> {$badge}");
                                })
                                ->columnSpan(2),

                            TextEntry::make('created_at_entry')
                                ->hiddenLabel()
                                ->state(function ($record) {
                                    $value = $record->created_at?->format('Y-m-d H:i') ?: '—';

                                    return new HtmlString(
                                        "<strong>Created at:</strong> {$value}"
                                    );
                                })
                                ->columnSpan(3),

                            TextEntry::make('created_by_user')
                                ->hiddenLabel()
                                ->state(function ($record) {
                                        $value = $record->user?->name ?? '-';
                                        

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
                    ->getStateUsing(fn (Business $record) =>
                        $record->coverages?->pluck('acronym')->filter()->unique()->values()->all() ?? []
                    )
                    ->tooltip(function (Business $record) {
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
                
                TextColumn::make('premium_type')
                    ->label('Premium Type')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('source_code')
                    ->label('Source id')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),   

                TextColumn::make('user.name')
                    ->label('Created by')
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
                    ->formatStateUsing(fn ($state) => $state?->value ?? $state)
                    ->color(function ($state) {
                        $value = $state?->value ?? $state;

                        return match ($value) {
                            'On Hold'   => 'gray',
                            'Pending'   => 'warning',
                            'In Force'  => 'success',
                            'To Expire' => 'warning',
                            'Expired'   => 'danger',
                            'Cancelled' => 'gray',
                            default     => 'secondary',
                        };
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
                    ->schema([
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

/* ->headerActions([
    Action::make('export')
        ->label('Export Report')
        ->icon('heroicon-o-arrow-down-tray')
        ->modalHeading('Export Reports')
        ->modalSubmitActionLabel('Generate')
        ->closeModalByClickingAway(false)
        ->closeModalByEscaping(false)
        ->form([
            Select::make('report_type')
                ->label('Report Type')
                ->options([
                    'operative_docs'      => 'Underwritten – Coverage Period',
                    'underwritten_report' => 'Underwritten – Reporting Month',
                ])
                ->default('operative_docs')
                ->required()
                ->live(),

            Select::make('reinsurer_ids')
                ->label('Reinsurer(s)')
                ->placeholder('All reinsurers')
                ->options(fn () => \App\Models\Reinsurer::orderBy('name')->pluck('name', 'id'))
                ->searchable()
                ->preload()
                ->multiple(),

            // =========================
            // Coverage Period (inception)
            // =========================
            DatePicker::make('from_date')
                ->label('From date')
                ->required(fn ($get) => $get('report_type') === 'operative_docs')
                ->visible(fn ($get) => $get('report_type') === 'operative_docs')
                ->native(false),

            DatePicker::make('to_date')
                ->label('To date')
                ->required(fn ($get) => $get('report_type') === 'operative_docs')
                ->visible(fn ($get) => $get('report_type') === 'operative_docs')
                ->native(false),

            // =========================
            // Reporting Month Range (rep_date)
            // =========================
            DatePicker::make('rep_from')
                ->label('Reporting month from')
                ->displayFormat('F Y')     // February 2026
                ->format('Y-m-01')         // guarda día 01
                ->required(fn ($get) => $get('report_type') === 'underwritten_report')
                ->visible(fn ($get) => $get('report_type') === 'underwritten_report')
                ->native(false)
                ->closeOnDateSelection()
                ->live(),

            DatePicker::make('rep_to')
                ->label('Reporting month to')
                ->displayFormat('F Y')
                ->format('Y-m-01')
                ->required(fn ($get) => $get('report_type') === 'underwritten_report')
                ->visible(fn ($get) => $get('report_type') === 'underwritten_report')
                ->native(false)
                ->closeOnDateSelection()
                ->live(),
        ])
        ->action(function (array $data) {

            $report = $data['report_type'] ?? null;

            $reinsurerIds = collect($data['reinsurer_ids'] ?? [])
                ->filter()
                ->values();

            $scope = $reinsurerIds->isEmpty()
                ? 'all-reinsurers'
                : ('reinsurers-' . $reinsurerIds->implode('-'));

            $reportLabels = [
                'operative_docs'      => 'OperativeDocs_report',
                'underwritten_report' => 'Underwritten_report',
            ];
            $reportLabel  = $reportLabels[$report] ?? ($report ?? 'report');

            // -----------------------------
            // Determine date range & column
            // -----------------------------
            if ($report === 'operative_docs') {
                $from = $data['from_date'] ?? null;
                $to   = $data['to_date'] ?? null;

                if (!$from || !$to) {
                    Notification::make()
                        ->title('Please select both dates.')
                        ->warning()
                        ->send();
                    return;
                }

                $rangeLabelFrom = Carbon::parse($from)->format('Ymd');
                $rangeLabelTo   = Carbon::parse($to)->format('Ymd');

                $dateColumn = 'operative_docs.inception_date';
                $dateStart  = Carbon::parse($from)->startOfDay();
                $dateEnd    = Carbon::parse($to)->endOfDay();
            }
            elseif ($report === 'underwritten_report') {
                $repFrom = $data['rep_from'] ?? null;
                $repTo   = $data['rep_to'] ?? null;

                if (!$repFrom || !$repTo) {
                    Notification::make()
                        ->title('Please select a reporting month range.')
                        ->warning()
                        ->send();
                    return;
                }

                $repFromC = Carbon::parse($repFrom)->startOfMonth();
                $repToC   = Carbon::parse($repTo)->endOfMonth();

                if ($repFromC->gt($repToC)) {
                    Notification::make()
                        ->title('Reporting month "from" must be before "to".')
                        ->warning()
                        ->send();
                    return;
                }

                // ✅ para el query (meses completos)
                $dateColumn = 'operative_docs.rep_date';
                $dateStart  = $repFromC->startOfDay();
                $dateEnd    = $repToC->endOfDay();

                // ✅ para el nombre del archivo: Jan2026_to_Feb2026
                $rangeLabelFrom = $repFromC->format('MY'); // Jan2026
                $rangeLabelTo   = $repToC->format('MY');   // Feb2026
            }
            else {
                Notification::make()
                    ->title('Unsupported report type.')
                    ->danger()
                    ->send();
                return;
            }

            // -----------------------------
            // Filename
            // -----------------------------
            if ($report === 'underwritten_report') {
                // ✅ Underwritten_report_Jan2026_to_Feb2026.xlsx
                $filename = sprintf(
                    'Underwritten_report_%s_to_%s.xlsx',
                    $rangeLabelFrom,
                    $rangeLabelTo
                );
            } else {
                // ✅ OperativeDocs_report_{scope}_YYYYMMDD_to_YYYYMMDD.xlsx (como ya lo traías)
                $filename = sprintf(
                    '%s_%s_%s_to_%s.xlsx',
                    $reportLabel,
                    $scope,
                    $rangeLabelFrom,
                    $rangeLabelTo
                );
            }

            // ---------------------------------------------------------
            // Flat query: 1 registro por (insured_row_id × node)
            // ---------------------------------------------------------
            $flat = OperativeDoc::query()
                ->with([
                    'business.reinsurer',
                    'business.currency',
                    'business.liabilityStructures',
                    'docType',
                ])
                ->join('businesses', 'operative_docs.business_code', '=', 'businesses.business_code')
                ->when($reinsurerIds->isNotEmpty(), fn ($q) =>
                    $q->whereIn('businesses.reinsurer_id', $reinsurerIds)
                )

                // ✅ filtro dinámico por columna (inception_date vs rep_date)
                ->whereBetween($dateColumn, [$dateStart, $dateEnd])

                // insureds
                ->leftJoin('businessdoc_insureds', 'businessdoc_insureds.op_document_id', '=', 'operative_docs.id')
                ->leftJoin('companies', 'companies.id', '=', 'businessdoc_insureds.company_id')
                ->leftJoin('countries', 'countries.id', '=', 'companies.country_id')
                ->leftJoin('coverages', 'coverages.id', '=', 'businessdoc_insureds.coverage_id')

                // scheme del insured
                ->leftJoin('cost_schemes as insured_scheme', 'insured_scheme.id', '=', 'businessdoc_insureds.cscheme_id')

                // nodes
                ->leftJoin('cost_nodesx', 'cost_nodesx.cscheme_id', '=', 'insured_scheme.id')

                // deductions label
                ->leftJoin('deductions', 'deductions.id', '=', 'cost_nodesx.concept')

                // partner source
                ->leftJoin('partners as p_src', 'p_src.id', '=', 'cost_nodesx.partner_source_id')

                ->orderBy('businesses.business_code')
                ->orderBy('operative_docs.id')
                ->orderBy('businessdoc_insureds.id')
                ->orderBy('cost_nodesx.index')

                ->select([
                    'operative_docs.*',

                    'insured_scheme.share as share',

                    'companies.name as insured_name',
                    'countries.name as country_name',
                    'coverages.name as coverage_name',
                    'businessdoc_insureds.premium as insured_premium',

                    'businessdoc_insureds.id as insured_row_id',
                    'businessdoc_insureds.cscheme_id as insured_cscheme_id',

                    'cost_nodesx.id as node_id',
                    'cost_nodesx.cscheme_id as node_cscheme_id',
                    'cost_nodesx.index as node_index',
                    'cost_nodesx.value as node_value',

                    'deductions.concept as deduction_concept',

                    'p_src.name as node_source_name',
                    'p_src.acronym as node_source_acronym',
                ])
                ->get();

            if ($flat->isEmpty()) {
                Notification::make()
                    ->title('No records found for the selected range.')
                    ->info()
                    ->send();
                return;
            }

            // ---------------------------------------------------------
            // Build wide (1 row per insured)
            // ---------------------------------------------------------
            $wide = $flat
                ->groupBy(fn ($r) => $r->insured_row_id ?? ($r->id . '|no-insured'))
                ->map(function ($rows) {
                    $first = $rows->first();

                    $schemeId = $first->insured_cscheme_id;

                    $schemeNodes = $rows
                        ->filter(fn ($r) => $schemeId && ($r->node_cscheme_id ?? null) === $schemeId)
                        ->unique('node_id')
                        ->sortBy(fn ($r) => (int) ($r->node_index ?? 0))
                        ->values();

                    $first->nodes_list = $schemeNodes->map(function ($r) {
                        $source = trim(($r->node_source_name ?? '') . ' - [' . ($r->node_source_acronym ?? '') . ']');
                        if ($source === '- []') {
                            $source = null;
                        }

                        return [
                            'deduction_type' => $r->deduction_concept ?? null,
                            'source'         => $source ?: null,
                            'value'          => is_null($r->node_value) ? null : (float) $r->node_value,
                        ];
                    })->all();

                    return $first;
                })
                ->values();

            $maxNodes = (int) ($wide
                ->map(fn ($d) => is_array($d->nodes_list ?? null) ? count($d->nodes_list) : 0)
                ->max() ?? 0);

            // Export (tu OperativeDocsExport no cambia)
            return Excel::download(
                new \App\Exports\OperativeDocsExport($wide, $maxNodes),
                $filename
            );
        }),
])
 */


            ->recordActions([
                ActionGroup::make([
                    // ───────── MAIN ─────────

                    ViewAction::make()
                        ->label('View')
                        //->color('primary')
                        ->url(fn (Business $record) =>
                            self::getUrl('view', ['record' => $record])
                        )
                        ->icon('heroicon-m-eye'),  // opcional

                    EditAction::make()
                        ->color('gray'),



                    // ───────── UPCOMING ─────────

                    Action::make('divider_1')
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
                            /** @var User|null $user */
                            $user = Filament::auth()->user();

                            return ! ($user?->can('Business:TechnicalResult') ?? false);
                        })
                        ->tooltip(function (): string {
                            /** @var User|null $user */
                            $user = Filament::auth()->user();

                            return ($user?->can('Business:TechnicalResult') ?? false)
                                ? 'View technical result'
                                : 'You do not have permission to access Technical Result';
                        })
                        ->action(function (): void {
                            /** @var User|null $user */
                            $user = Filament::auth()->user();

                            if (! ($user?->can('Business:TechnicalResult') ?? false)) {
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
                            /** @var User|null $user */
                            $user = Filament::auth()->user();

                            return ! ($user?->can('Business:Renewal') ?? false);
                        })
                        ->tooltip(function (): string {
                            /** @var User|null $user */
                            $user = Filament::auth()->user();

                            return ($user?->can('Business:Renewal') ?? false)
                                ? 'Renew this business'
                                : 'You do not have permission to renew this business';
                        })
                        ->action(function (): void {
                            /** @var User|null $user */
                            $user = Filament::auth()->user();

                            if (! ($user?->can('Business:Renewal') ?? false)) {
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

                    Action::make('divider_1')
                        ->label('')
                        ->disabled()
                        ->extraAttributes([
                            'class' => 'pointer-events-none border-t border-gray-700 my-1',
                            'style' => 'height: 0; padding: 0; margin: 3px 0;',
                        ]),

                    DeleteAction::make(),
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
            LiabilityStructuresRelationManager::class,
            OperativeDocsRelationManager::class,        
        ];
    }



    public static function getPages(): array
    {
        return [
            'index' => ListBusinesses::route('/'),
            'create' => CreateBusiness::route('/create'),
            'edit' => EditBusiness::route('/{record}/edit'),
            'view' => ViewBusiness::route('/{record}/view'), // 👈 Asegúrate que esto esté
            
        ];
    }


    public static function getWidgets(): array
    {
        return [
            BusinessStatsOverview::class,
        ];
    }




}
