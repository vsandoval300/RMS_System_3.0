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
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\BulkActionGroup;
use App\Filament\Resources\Businesses\RelationManagers\LiabilityStructuresRelationManager;
use App\Filament\Resources\Businesses\RelationManagers\OperativeDocsRelationManager;
use App\Filament\Resources\Businesses\Pages\ListBusinesses;
use App\Filament\Resources\Businesses\Pages\CreateBusiness;
use App\Filament\Resources\Businesses\Pages\EditBusiness;
use App\Filament\Resources\Businesses\Pages\ViewBusiness;
use App\Filament\Resources\Businesses\Pages\ImportBusinesses;
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
use Filament\Facades\Filament;
use App\Models\User;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Illuminate\Support\HtmlString;
use App\Services\BusinessRenewalService;
use App\Services\BusinessTechnicalResultService;
use Illuminate\Support\Facades\Gate;
use Filament\Infolists\Components\ViewEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Group as ComponentsGroup;
use App\Enums\ApprovalStatus;
use Filament\Schemas\Components\Utilities\Get;

class BusinessResource extends Resource
{
    protected static ?string $model = Business::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-minus';
    protected static string | \UnitEnum | null $navigationGroup = 'Underwritten';
    protected static ?int    $navigationSort  = 18;

    // ── Shared badge renderer for approval_status ──────────────────────────────
    public static function approvalStatusBadgeHtml(mixed $status): string
    {
        $value = $status?->value ?? ($status instanceof \BackedEnum ? $status->value : $status);

        [$label, $bg, $color] = match ($value) {
            'DFT'   => ['Draft',          'light-dark(#f3f4f6,#27272a)', 'light-dark(#374151,#9ca3af)'],
            'PND'   => ['Pending Review', 'light-dark(#fef9c3,#1c1a0e)', 'light-dark(#854d0e,#fbbf24)'],
            'APR'   => ['Approved',       'light-dark(#dcfce7,#052e16)', 'light-dark(#166534,#86efac)'],
            'REJ'   => ['Needs Revision', 'light-dark(#fee2e2,#1c0a0a)', 'light-dark(#991b1b,#fca5a5)'],
            'CAN'   => ['Cancelled',      'light-dark(#f3f4f6,#27272a)', 'light-dark(#6b7280,#9ca3af)'],
            default => ['—',              'light-dark(#f3f4f6,#27272a)', 'light-dark(#6b7280,#9ca3af)'],
        };

        return "<span style=\"
            display:inline-flex; align-items:center; gap:0.35rem;
            background:{$bg}; color:{$color};
            font-size:0.78rem; font-weight:600;
            padding:0.2rem 0.65rem; border-radius:9999px;
            letter-spacing:0.03em;
        \">{$label}</span>";
    }



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
            ->withCount(['operativeDocs'])
            ->withExists([
                'operativeDocs as has_cancellation_doc' => fn ($q) => $q->where('operative_doc_type_id', 5),
            ])
            ->withExists('renewals as has_renewals')
            ->withMax('operativeDocs', 'expiration_date');
    }


    /* =========================
     *  FORM  (create / edit)
     * ========================= */
    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([

                // ── Revision notes alert (visible only when status = REJ) ──────────
                Placeholder::make('revision_alert')
                    ->hiddenLabel()
                    ->columnSpanFull()
                    ->visible(fn ($record) =>
                        $record?->approval_status === ApprovalStatus::REJECTED &&
                        filled($record?->revision_notes)
                    )
                    ->content(fn ($record) => new HtmlString('
                        <div style="
                            background: light-dark(#fff7ed, #1c1206);
                            border: 1px solid light-dark(#fb923c, #c2410c);
                            border-radius: 0.6rem;
                            padding: 1rem 1.25rem;
                            display: flex;
                            gap: 0.75rem;
                            align-items: flex-start;
                        ">
                            <svg style="flex-shrink:0;width:20px;height:20px;color:light-dark(#ea580c,#fb923c);margin-top:2px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
                            </svg>
                            <div style="flex:1;">
                                <div style="font-weight:700; font-size:0.9rem; color:light-dark(#9a3412,#fb923c); margin-bottom:0.35rem;">
                                    Revision Required
                                </div>
                                <div style="font-size:0.875rem; color:light-dark(#7c2d12,#fed7aa); white-space:pre-wrap; line-height:1.5;">
                                    ' . e($record?->revision_notes) . '
                                </div>
                                <div style="font-size:0.775rem; color:light-dark(#c2410c,#fb923c); margin-top:0.5rem;">
                                    Reviewed by: <strong>' . e($record?->reviewer?->name ?? '—') . '</strong>
                                    &nbsp;·&nbsp;
                                    ' . ($record?->approval_status_updated_at?->format('M d, Y') ?? '') . '
                                </div>
                            </div>
                        </div>
                    ')),

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
                                            ->label('Source Id')
                                            ->placeholder('Enter original id if necessary.')
                                            ->columnSpan(3),

                                        TextInput::make('policy_number')
                                            ->label('Policy Number')
                                            ->placeholder('Enter original policy id if necessary.')
                                            ->helperText('Enter the original policy number issued by the insurer, if available.')
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

                                Placeholder::make('approval_status_badge')
                                    ->label('Approval Status')
                                    ->content(fn ($record) => new HtmlString(
                                        self::approvalStatusBadgeHtml($record?->approval_status)
                                    )),

                                DatePicker::make('approval_status_updated_at')
                                    ->label('Approval Date')
                                    ->disabled(),
                            ]),
                    ]),

                Section::make('Territorial Coverage')
                    ->columnSpan('full')
                    ->description('Geographic scope of this business contract.')
                    ->schema([
                        Select::make('region_id')
                            ->label('Region')
                            ->relationship('region', 'name')
                            ->searchable()
                            ->preload()
                            ->default(2)
                            ->required()
                            ->live(),

                        Placeholder::make('region_map')
                            ->hiddenLabel()
                            ->columnSpanFull()
                            ->content(fn () => new HtmlString(
                                view('filament.components.region-map')->render()
                            )),
                    ]),
            ]);
    }










    /* =========================
     *  INFOLIST  (VIEW PAGE)
     * ========================= */
    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([

            // ── Revision notes alert (visible only when status = REJ) ──────────
            TextEntry::make('revision_alert')
                ->hiddenLabel()
                ->columnSpanFull()
                ->visible(fn ($record) =>
                    $record?->approval_status === ApprovalStatus::REJECTED &&
                    filled($record?->revision_notes)
                )
                ->html()
                ->state(fn ($record) => '
                    <div style="
                        background: light-dark(#fff7ed, #1c1206);
                        border: 1px solid light-dark(#fb923c, #c2410c);
                        border-radius: 0.6rem;
                        padding: 1rem 1.25rem;
                        display: flex;
                        gap: 0.75rem;
                        align-items: flex-start;
                    ">
                        <svg style="flex-shrink:0;width:20px;height:20px;color:light-dark(#ea580c,#fb923c);margin-top:2px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
                        </svg>
                        <div style="flex:1;">
                            <div style="font-weight:700; font-size:0.9rem; color:light-dark(#9a3412,#fb923c); margin-bottom:0.35rem;">
                                Revision Required
                            </div>
                            <div style="font-size:0.875rem; color:light-dark(#7c2d12,#fed7aa); white-space:pre-wrap; line-height:1.5;">
                                ' . e($record?->revision_notes) . '
                            </div>
                            <div style="font-size:0.775rem; color:light-dark(#c2410c,#fb923c); margin-top:0.5rem;">
                                Reviewed by: <strong>' . e($record?->reviewer?->name ?? '—') . '</strong>
                                &nbsp;·&nbsp;
                                ' . ($record?->approval_status_updated_at?->format('M d, Y') ?? '') . '
                            </div>
                        </div>
                    </div>
                '),

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

                                                    TextEntry::make('policy_number')
                                                        ->hiddenLabel()
                                                        ->state(function ($record) {
                                                            $value = $record->policy_number ?: '—';

                                                            return new HtmlString(
                                                                "<strong>Policy Number:</strong> {$value}"
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
                                ->state(fn ($record) => new HtmlString(
                                    '<div style="display:flex;align-items:center;gap:8px;">'
                                    . '<span style="font-weight:600;">Approval status:</span>'
                                    . self::approvalStatusBadgeHtml($record?->approval_status)
                                    . '</div>'
                                ))
                                ->extraAttributes(['style' => 'display:flex;align-items:center;'])
                                ->columnSpan(2),

                            TextEntry::make('approval_date_entry')
                                ->hiddenLabel()
                                ->state(function ($record) {
                                    $value = $record->approval_status_updated_at?->format('d/m/Y') ?: '—';

                                    return new HtmlString(
                                        "<strong>Approval date:</strong>&nbsp;{$value}"
                                    );
                                })
                                ->extraAttributes(['style' => 'display:flex;align-items:center;'])
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

                                    $badge = "<span style=\"display:inline-flex;align-items:center;padding:2px 10px;border-radius:9999px;font-size:14px;font-weight:500;background-color:{$bg};color:{$text};margin-left:6px;\">{$value}</span>";

                                    return new HtmlString("<strong>Lifecycle status:</strong> {$badge}");
                                })
                                ->extraAttributes(['style' => 'display:flex;align-items:center;'])
                                ->columnSpan(2),

                            TextEntry::make('created_at_entry')
                                ->hiddenLabel()
                                ->state(function ($record) {
                                    $value = $record->created_at?->format('d/m/Y H:i') ?: '—';

                                    return new HtmlString(
                                        "<strong>Created at:</strong>&nbsp;{$value}"
                                    );
                                })
                                ->extraAttributes(['style' => 'display:flex;align-items:center;'])
                                ->columnSpan(3),

                            TextEntry::make('created_by_user')
                                ->hiddenLabel()
                                ->state(function ($record) {
                                        $value = $record->user?->name ?? '-';

                                        return new HtmlString(
                                            "<strong>Created by:</strong>&nbsp;{$value}"
                                        );
                                    })
                                ->extraAttributes(['style' => 'display:flex;align-items:center;'])
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
            ->persistFiltersInSession()
            ->persistColumnsInSession()
            ->persistSearchInSession()
            ->persistColumnSearchesInSession()
            ->persistSortInSession()
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

                TextColumn::make('approval_status')
                    ->label('Approval')
                    ->badge()
                    ->formatStateUsing(fn ($state) => match ($state?->value ?? $state) {
                        'DFT' => 'Draft',
                        'PND' => 'Pending Review',
                        'APR' => 'Approved',
                        'REJ' => 'Needs Revision',
                        'CAN' => 'Cancelled',
                        default => $state?->value ?? '—',
                    })
                    ->color(fn ($state) => match ($state?->value ?? $state) {
                        'DFT' => 'gray',
                        'PND' => 'warning',
                        'APR' => 'success',
                        'REJ' => 'danger',
                        'CAN' => 'gray',
                        default => 'gray',
                    })
                    ->sortable(),

                TextColumn::make('business_lifecycle_status')
                    ->extraAttributes(['class' => 'rms-small-desc'])
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
                    ->description(function ($record): ?string {
                        if ($record->operativeDocs()->where('operative_doc_type_id', 5)->exists()) {
                            return null;
                        }
                        if ($record->renewals()->exists()) {
                            return null;
                        }
                        $maxExp = $record->operativeDocs()->max('expiration_date');
                        if (! $maxExp) {
                            return null;
                        }
                        $expDate = \Carbon\Carbon::parse($maxExp);
                        if (! now()->between($expDate->copy()->subDays(45), $expDate->copy()->addDays(45))) {
                            return null;
                        }
                        return '↻ Ready to renew';
                    })
                    ->searchable()
                    ->sortable(),

                TextColumn::make('operative_docs_count')
                    ->counts('operativeDocs')
                    ->extraAttributes(['class' => 'rms-small-desc'])
                    ->label('Documents')
                    ->sortable()
                    ->formatStateUsing(fn ($state) => "$state document" . ($state === 1 ? '' : 's'))
                    ->badge()
                    ->color(fn (int $state): string => $state > 0 ? 'primary' : 'gray')
                    ->description(function ($record): ?string {
                        $missing = $record->missing_pdf_count ?? 0;
                        if ($missing <= 0) return null;
                        return "⚠ {$missing} missing pdf" . ($missing === 1 ? '' : 's');
                    }),



            ])
            ->filters([
                // 🔹 Filtro por Reinsurer
                SelectFilter::make('reinsurer_id')
                    ->label('Reinsurer')
                    ->relationship('reinsurer', 'short_name')
                    ->searchable()
                    ->preload(),

                // 🔹 Filtro por intervalo de tiempo (created_at)
                Filter::make('date_interval')
                    ->label('Time interval')
                    ->schema([
                        Select::make('interval')
                            ->label('Time interval')
                            ->options([
                                '30'     => 'Last 30 days',
                                '90'     => 'Last 3 months',
                                '180'    => 'Last 6 months',
                                '365'    => 'Last 12 months',
                                'custom' => 'Custom',
                            ])
                            ->placeholder('All time')
                            ->live(),
                        DatePicker::make('from')
                            ->label('From')
                            ->visible(fn (Get $get) => $get('interval') === 'custom')
                            ->displayFormat('d/m/Y'),
                        DatePicker::make('until')
                            ->label('To')
                            ->visible(fn (Get $get) => $get('interval') === 'custom')
                            ->displayFormat('d/m/Y'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        $interval = $data['interval'] ?? null;
                        if (! $interval) {
                            return $query;
                        }
                        if ($interval === 'custom') {
                            return $query
                                ->when(
                                    $data['from'] ?? null,
                                    fn ($q, $date) => $q->whereDate('created_at', '>=', $date)
                                )
                                ->when(
                                    $data['until'] ?? null,
                                    fn ($q, $date) => $q->whereDate('created_at', '<=', $date)
                                );
                        }
                        return $query->where('created_at', '>=', now()->subDays((int) $interval));
                    })
                    ->indicateUsing(function (array $data): array {
                        $interval = $data['interval'] ?? null;
                        if (! $interval) {
                            return [];
                        }
                        $labels = [
                            '30'     => 'Last 30 days',
                            '90'     => 'Last 3 months',
                            '180'    => 'Last 6 months',
                            '365'    => 'Last 12 months',
                            'custom' => 'Custom',
                        ];
                        $indicators = ['Time interval: ' . ($labels[$interval] ?? $interval)];
                        if ($interval === 'custom') {
                            if ($data['from'] ?? null) {
                                $indicators[] = 'From: ' . Carbon::parse($data['from'])->format('d/m/Y');
                            }
                            if ($data['until'] ?? null) {
                                $indicators[] = 'To: ' . Carbon::parse($data['until'])->format('d/m/Y');
                            }
                        }
                        return $indicators;
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
                        ->label('Business Summary')
                        ->icon('heroicon-m-calculator')
                        ->color('primary')
                        ->disabled(fn () => ! Gate::allows('Business:TechnicalResult'))
                        ->tooltip(fn () => Gate::allows('Business:TechnicalResult')
                            ? 'View business summary'
                            : 'You do not have permission to access Business Summary'
                        )
                        ->modalHeading(fn (Business $record) => "Business Summary — {$record->business_code}")
                        ->modalContent(function (Business $record): \Illuminate\Contracts\View\View {
                            $data = app(BusinessTechnicalResultService::class)->build($record);
                            return view('filament.resources.business.technical-result', $data);
                        })
                        ->modalWidth('7xl')
                        ->modalSubmitAction(false)
                        ->modalCancelActionLabel('Close'),


                    Action::make('renewal')
                        ->label('Renewal')
                        ->icon('heroicon-m-arrow-path')
                        ->color('primary')
                        ->visible(function (Business $record): bool {
                            // Never show if there is a cancellation document (type 5)
                            if ($record->operativeDocs()->where('operative_doc_type_id', 5)->exists()) {
                                return false;
                            }
                            // Never show if this business has already been renewed
                            if ($record->renewals()->exists()) {
                                return false;
                            }
                            // Only show within ±45 days of the latest operative doc expiration date
                            $maxExp = $record->operativeDocs()->max('expiration_date');
                            if (! $maxExp) {
                                return false;
                            }
                            $expDate = \Carbon\Carbon::parse($maxExp);
                            $now = now();
                            return $now->between(
                                $expDate->copy()->subDays(45),
                                $expDate->copy()->addDays(45)
                            );
                        })
                        ->disabled(fn () => ! \Illuminate\Support\Facades\Gate::allows('Business:Renewal'))
                        ->tooltip(fn () => \Illuminate\Support\Facades\Gate::allows('Business:Renewal')
                            ? 'Renew this business'
                            : 'You do not have permission to renew this business'
                        )
                        ->modalHeading('Renew Business')
                        ->modalDescription('A new business will be created as a renewal of the current one. Review the details below before confirming.')
                        ->modalWidth('lg')
                        ->form(function (Business $record): array {
                            $service = app(BusinessRenewalService::class);
                            $dates   = $service->previewSlipDates($record);

                            return [
                                TextInput::make('new_business_code')
                                    ->label('New Business Code')
                                    ->default(fn () => $service->suggestBusinessCode($record))
                                    ->required()
                                    ->readOnly()
                                    ->unique(Business::class, 'business_code')
                                    ->helperText('Auto-generated based on the original code and new inception year.'),

                                DatePicker::make('new_inception_date')
                                    ->label('New Slip — Inception Date')
                                    ->default($dates ? $dates['inception']->toDateString() : null)
                                    ->required(fn () => $dates !== null)
                                    ->hidden(fn () => $dates === null),

                                DatePicker::make('new_expiration_date')
                                    ->label('New Slip — Expiration Date')
                                    ->default($dates ? $dates['expiration']->toDateString() : null)
                                    ->required(fn () => $dates !== null)
                                    ->hidden(fn () => $dates === null)
                                    ->after('new_inception_date'),

                                Placeholder::make('no_slip_warning')
                                    ->label('')
                                    ->content(new HtmlString(
                                        '<span style="color:#f87171;">⚠ No Slip document found. Operative document will not be created.</span>'
                                    ))
                                    ->hidden(fn () => $dates !== null),

                                Placeholder::make('original_info')
                                    ->label('Renewing from')
                                    ->content(new HtmlString(
                                        '<strong>' . $record->business_code . '</strong>'
                                        . ' — ' . e($record->description ?? '')
                                    )),
                            ];
                        })
                        ->action(function (Business $record, array $data): void {
                            if (! \Illuminate\Support\Facades\Gate::allows('Business:Renewal')) {
                                Notification::make()
                                    ->title('Permission denied')
                                    ->body('You do not have permission to renew this business.')
                                    ->danger()
                                    ->send();
                                return;
                            }

                            try {
                                $newBusiness = app(BusinessRenewalService::class)
                                    ->renew(
                                        $record,
                                        $data['new_business_code'],
                                        $data['new_inception_date'] ?? null,
                                        $data['new_expiration_date'] ?? null,
                                    );

                                Notification::make()
                                    ->title('Business renewed successfully')
                                    ->body("Business {$newBusiness->business_code} has been created as a renewal of {$record->business_code}.")
                                    ->success()
                                    ->send();

                                redirect(self::getUrl('view', ['record' => $newBusiness->business_code]));

                            } catch (\Throwable $e) {
                                Notification::make()
                                    ->title('Renewal failed')
                                    ->body($e->getMessage())
                                    ->danger()
                                    ->persistent()
                                    ->send();
                            }
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


            ])
            ->headerActions([
                Action::make('column_guide')
                    ->label('Column guide')
                    ->icon('heroicon-o-question-mark-circle')
                    ->color('gray')
                    ->slideOver()
                    ->modalHeading('Understanding This Table')
                    ->modalContent(view('filament.resources.business.table-column-guide'))
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Close'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->requiresConfirmation()
                        ->modalHeading('Delete selected businesses')
                        ->modalDescription('Are you sure you want to delete the selected businesses? This action can be undone by restoring the records.')
                        ->modalSubmitActionLabel('Yes, delete'),
                ]),
            ])
            ->defaultPaginationPageOption(25)
            ->paginationPageOptions([5, 10, 25, 50])
            ->modifyQueryUsing(fn ($query) => $query->withCount([
                'operativeDocs as missing_pdf_count' => fn ($q) => $q->whereNull('document_path'),
            ]));
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
            'index'  => ListBusinesses::route('/'),
            'create' => CreateBusiness::route('/create'),
            'edit'   => EditBusiness::route('/{record}/edit'),
            'view'   => ViewBusiness::route('/{record}/view'),
            'import' => ImportBusinesses::route('/import'),
            
        ];
    }


    public static function getWidgets(): array
    {
        return [
            BusinessStatsOverview::class,
        ];
    }




}
