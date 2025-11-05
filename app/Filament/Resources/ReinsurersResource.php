<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReinsurersResource\Pages;
use App\Filament\Resources\ReinsurersResource\RelationManagers;
use App\Models\Country;
use App\Models\Reinsurer;
use App\Models\OperativeStatus;
use App\Models\ReinsurerType;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\FileUpload;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile; // Livewire v3
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DatePicker;
use Maatwebsite\Excel\Facades\Excel;
use Filament\Notifications\Notification;
use App\Exports\ReinsurersExport;
use Carbon\Carbon;
use Filament\Tables\Actions\Action;
use Illuminate\Validation\Rule;

// 👇 IMPORTS para INFOLIST
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section as InfoSection;
use Filament\Infolists\Components\Grid as InfoGrid;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;



class ReinsurersResource extends Resource
{
    protected static ?string $model = Reinsurer::class;
    protected static ?string $navigationIcon = 'heroicon-o-minus';
    protected static ?string $navigationGroup = 'Reinsurers';
    protected static ?int    $navigationSort  = 1;  

    /* ───── NUEVO: burbuja con el total en el menú ───── */
    public static function getNavigationBadge(): ?string
    {
        // Puedes usar self::$model::count() o Reinsurer::count()
        return Reinsurer::count();
    }

    public static function getTableQuery(): Builder
    {
        return Reinsurer::query()->with([
            'parent',
            'reinsurer_type',
            'country',
            'operative_status',
        ]);
    }




    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                // ========== DETAILS ==========
                Section::make('Details')
                    ->columns(2)
                    ->schema([

                        Section::make() // 👈 sin título
                            ->columns(6)
                            ->schema([
                                TextInput::make('id')
                                    ->label('ID')
                                    ->default(fn ($record) => $record?->id ?? '(pending)')
                                    ->readOnly()
                                    ->disabled(),

                                TextInput::make('cns_reinsurer')
                                    ->label('LSK (Legacy Substitute Key)')
                                    ->placeholder("Please provide LSK number if exist.")
                                    ->unique(ignoreRecord: true)
                                    ->nullable()
                                    ->columnSpan(2),
                            ])
                            ->compact(),
                     ]),



                        Section::make('Basic Info')
                            ->columns(6)
                            ->schema([
                                TextInput::make('name')
                                    ->label('Name')
                                    ->placeholder("Please provide reinsurer's name")
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->rules([
                                        Rule::unique('reinsurers', 'name')
                                            ->whereNull('deleted_at'),
                                    ])
                                    ->maxLength(255)
                                    ->live(onBlur: true) // 👈 dispara solo cuando se pierde el foco
                                    ->afterStateUpdated(function ($state, callable $set) {
                                        if (blank($state)) {
                                            return;
                                        }

                                        $exceptions = ['de', 'of', 'y'];

                                        $formatted = preg_replace_callback(
                                            '/\b(\p{L})(\p{L}*)/u',
                                            function ($matches) use ($exceptions) {
                                                $word = $matches[0];

                                                if (in_array(mb_strtolower($word), $exceptions)) {
                                                    return mb_strtolower($word);
                                                }

                                                return mb_strtoupper($matches[1]) . mb_substr($word, 1);
                                            },
                                            $state
                                        );

                                        $set('name', $formatted);
                                    })
                                    ->columnSpan(3),

                                TextInput::make('short_name')
                                    ->label('Short Name')
                                    ->placeholder("Please provide reinsurer's short name")
                                    ->rules([
                                        Rule::unique('reinsurers', 'short_name')
                                            ->whereNull('deleted_at'),
                                    ])
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(255)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function ($state, callable $set) {
                                        if (blank($state)) {
                                            return;
                                        }

                                        $exceptions = ['de', 'of', 'y'];

                                        $formatted = preg_replace_callback(
                                            '/\b(\p{L})(\p{L}*)/u',
                                            function ($matches) use ($exceptions) {
                                                $word = $matches[0];

                                                if (in_array(mb_strtolower($word), $exceptions)) {
                                                    return mb_strtolower($word);
                                                }

                                                return mb_strtoupper($matches[1]) . mb_substr($word, 1);
                                            },
                                            $state
                                        );

                                        $set('short_name', $formatted);
                                    })
                                    ->columnSpan(1),

                                TextInput::make('acronym')
                                    ->label('Acronym')
                                    ->placeholder('e.g. ABC')
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->rules([
                                        Rule::unique('reinsurers', 'short_name')
                                            ->whereNull('deleted_at'),
                                    ])
                                    ->maxLength(3)
                                    ->rule('regex:/^[A-Z]{3}$/')
                                    ->afterStateUpdated(fn ($state, callable $set) => $set('acronym', strtoupper($state)))
                                    ->helperText('Only three uppercase letters allowed.')
                                    ->columnSpan(1),

                                Select::make('reinsurer_type_id')
                                    ->label('Type')
                                    ->relationship(
                                        name: 'reinsurer_type',
                                        titleAttribute: 'type_acronym'
                                    )
                                    ->getOptionLabelFromRecordUsing(
                                        fn (ReinsurerType $record) => "{$record->type_acronym} - {$record->description}"
                                    )
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->placeholder('Select type')
                                    ->helperText('Choose reinsurer type.')
                                    ->columnSpan(2),

                                ToggleButtons::make('class')
                                    ->label('Class')
                                    ->options([
                                        'Class 1' => 'Class 1',
                                        'Class 2' => 'Class 2',
                                    ])
                                    ->required()
                                    ->inline()
                                    ->colors([
                                        'Class 1' => 'primary',
                                        'Class 2' => 'primary',
                                    ])
                                    ->helperText('Select reinsurer class.'),
                            ])
                            ->compact(),
                    

                                        // ========== ADMIN INFO ==========
                        Section::make('Administrative Info')
                            ->columns(6)
                            ->schema([
                                Select::make('parent_id')
                                    ->label('Parent')
                                    ->relationship(
                                        name: 'parent',
                                        titleAttribute: 'name',
                                        modifyQueryUsing: fn (Builder $query) => $query->orderBy('name'),
                                    )
                                    ->searchable()
                                    ->preload()
                                    ->optionsLimit(300)
                                    ->nullable()
                                    ->helperText('Select the parent reinsurer if applicable.')
                                    ->columnSpan(3),

                                Select::make('established')
                                    ->label('Established Year')
                                    ->options(
                                        collect(range(now()->year, 2010))->mapWithKeys(fn ($year) => [$year => $year])
                                    )
                                    ->searchable()
                                    ->required()
                                    ->placeholder('Select year')
                                    ->placeholder('e.g. 2015')
                                    ->helperText('Select year between 2010 and ' . now()->year . '.')
                                    ->required()
                                    ->columnSpan(1),

                                Select::make('country_id')
                                    ->label('Country')
                                    ->relationship(
                                        name: 'country',
                                        titleAttribute: 'alpha_3',
                                        modifyQueryUsing: fn (Builder $query) => $query->orderBy('alpha_3'),
                                    )
                                    ->getOptionLabelFromRecordUsing(
                                        fn (Country $record) => "{$record->alpha_3} - {$record->name}"
                                    )
                                    ->searchable()
                                    ->preload()
                                    ->optionsLimit(300)
                                    ->required()
                                    ->placeholder('Select a country')
                                    ->helperText('Choose the reinsurer\'s country.')
                                    ->columnSpan(2),

                                // 👇 segunda fila
                                Select::make('manager_id')
                                    ->label('Manager')
                                    ->relationship('manager', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->helperText('Select the manager assigned to this reinsurer.')
                                    ->placeholder('Select a manager')
                                    ->columnSpan(3),  // 👈 ocupa media fila (3 de 6)

                                Select::make('operative_status_id')
                                    ->label('Operative Status')
                                    ->relationship(
                                        name: 'operative_status',
                                        titleAttribute: 'acronym',
                                        modifyQueryUsing: fn (Builder $query) => $query->orderBy('acronym'),
                                    )
                                    ->getOptionLabelFromRecordUsing(
                                        fn (OperativeStatus $record) => "{$record->acronym} - {$record->description}"
                                    )
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->placeholder('Select status')
                                    ->helperText('Choose reinsurer’s operative status.')
                                    ->columnSpan(2),  // 👈 ocupa la otra media fila (3 de 6)
                            ])
                            ->compact(),


                // ========== IMAGES ==========
                Section::make('Branding')
                    ->columns(2)
                    ->schema([
                        Section::make('Logo Upload')
                            ->columnSpan(1)
                            ->schema([
                                FileUpload::make('logo')
                                    ->label('Reinsurer Logo')
                                    ->disk('s3')
                                    ->directory('reinsurers/logos')
                                    ->visibility('public')
                                    ->image()
                                    ->acceptedFileTypes(['image/png', 'image/jpeg', 'image/svg+xml'])
                                    ->preserveFilenames()
                                    ->previewable(false)
                                    ->downloadable()
                                    ->openable()
                                    ->hint(function ($record) {
                                        return $record?->logo
                                            ? 'Existing logo: ' . basename($record->logo)
                                            : 'No logo uploaded yet.';
                                    })
                                    ->helperText('Upload the reinsurer’s logo (PNG, JPG, or SVG, preferably square).')
                                    ->deleteUploadedFileUsing(function ($file) {
                                        if ($file && Storage::disk('s3')->exists($file)) {
                                            Storage::disk('s3')->delete($file);
                                        }
                                    }),
                            ])
                            ->compact(),

                        Section::make('Icon Upload')
                            ->columnSpan(1)
                            ->schema([
                                FileUpload::make('icon')
                                    ->label('Reinsurer Icon')
                                    ->disk('s3')
                                    ->directory('reinsurers/icons')
                                    ->visibility('public')
                                    ->image()
                                    ->acceptedFileTypes(['image/png', 'image/jpeg', 'image/svg+xml'])
                                    ->preserveFilenames()
                                    ->previewable(false)
                                    ->downloadable()
                                    ->openable()
                                    ->hint(function ($record) {
                                        return $record?->icon
                                            ? 'Existing icon: ' . basename($record->icon)
                                            : 'No icon uploaded yet.';
                                    })
                                    ->helperText('Upload the reinsurer’s icon (PNG, JPG, or SVG, preferably square).')
                                    ->deleteUploadedFileUsing(function ($file) {
                                        if ($file && Storage::disk('s3')->exists($file)) {
                                            Storage::disk('s3')->delete($file);
                                        }
                                    }),
                            ])
                            ->compact(),
                    ])
                    


            ]);
    }

    




public static function infolist(Infolist $infolist): Infolist
{
    return $infolist->schema([
        InfoSection::make('Reinsurer Profile')->schema([
            InfoGrid::make(3)
                ->extraAttributes(['style' => 'gap: 6px;'])
                ->schema([

                    /* ── Cols 1–2: filas compactas "Label + Value" ── */
                    InfoGrid::make(1)
                        ->columnSpan(2)
                        ->extraAttributes(['style' => 'row-gap: 0;'])
                        ->schema([

                            // Name
                            InfoGrid::make(12)
                                ->extraAttributes(['style' => 'border-bottom:1px solid rgba(255,255,255,0.12); padding:2px 0;'])
                                ->schema([
                                    TextEntry::make('name_label')->label('')
                                        ->state('Name:')->weight('bold')->alignment('right')
                                        ->columnSpan(3),
                                    TextEntry::make('name_value')->label('')
                                        ->state(fn ($record) => $record->name ?: '—')
                                        ->columnSpan(9),
                                ]),

                            // Short Name
                            InfoGrid::make(12)
                                ->extraAttributes(['style' => 'border-bottom:1px solid rgba(255,255,255,0.12); padding:2px 0;'])
                                ->schema([
                                    TextEntry::make('short_label')->label('')
                                        ->state('Short Name:')->weight('bold')->alignment('right')
                                        ->columnSpan(3),
                                    TextEntry::make('short_value')->label('')
                                        ->state(fn ($record) => $record->short_name ?: '—')
                                        ->columnSpan(9),
                                ]),

                            // Acronym
                            InfoGrid::make(12)
                                ->extraAttributes(['style' => 'border-bottom:1px solid rgba(255,255,255,0.12); padding:2px 0;'])
                                ->schema([
                                    TextEntry::make('acr_label')->label('')
                                        ->state('Acronym:')->weight('bold')->alignment('right')
                                        ->columnSpan(3),
                                    TextEntry::make('acr_value')->label('')
                                        ->state(fn ($record) => $record->acronym ?: '—')
                                        ->columnSpan(9),
                                ]),

                            // Type
                            InfoGrid::make(12)
                                ->extraAttributes(['style' => 'border-bottom:1px solid rgba(255,255,255,0.12); padding:2px 0;'])
                                ->schema([
                                    TextEntry::make('type_label')->label('')
                                        ->state('Type:')->weight('bold')->alignment('right')
                                        ->columnSpan(3),
                                    TextEntry::make('type_value')->label('')
                                        ->state(fn ($record) =>
                                            $record->reinsurer_type
                                                ? "{$record->reinsurer_type->type_acronym} - {$record->reinsurer_type->description}"
                                                : '—'
                                        )
                                        ->columnSpan(9),
                                ]),

                            // Class
                            InfoGrid::make(12)
                                ->extraAttributes(['style' => 'border-bottom:1px solid rgba(255,255,255,0.12); padding:2px 0;'])
                                ->schema([
                                    TextEntry::make('class_label')->label('')
                                        ->state('Class:')->weight('bold')->alignment('right')
                                        ->columnSpan(3),
                                    TextEntry::make('class_value')->label('')
                                        ->state(fn ($record) => $record->class ?: '—')
                                        ->columnSpan(9),
                                ]),

                            // Parent
                            InfoGrid::make(12)
                                ->extraAttributes(['style' => 'border-bottom:1px solid rgba(255,255,255,0.12); padding:2px 0;'])
                                ->schema([
                                    TextEntry::make('parent_label')->label('')
                                        ->state('Parent:')->weight('bold')->alignment('right')
                                        ->columnSpan(3),
                                    TextEntry::make('parent_value')->label('')
                                        ->state(fn ($record) => $record->parent?->name ?: '—')
                                        ->columnSpan(9),
                                ]),

                            // Established
                            InfoGrid::make(12)
                                ->extraAttributes(['style' => 'border-bottom:1px solid rgba(255,255,255,0.12); padding:2px 0;'])
                                ->schema([
                                    TextEntry::make('est_label')->label('')
                                        ->state('Established:')->weight('bold')->alignment('right')
                                        ->columnSpan(3),
                                    TextEntry::make('est_value')->label('')
                                        ->state(fn ($record) => $record->established ?: '—')
                                        ->columnSpan(9),
                                ]),

                            // Country
                            InfoGrid::make(12)
                                ->extraAttributes(['style' => 'border-bottom:1px solid rgba(255,255,255,0.12); padding:2px 0;'])
                                ->schema([
                                    TextEntry::make('country_label')->label('')
                                        ->state('Country:')->weight('bold')->alignment('right')
                                        ->columnSpan(3),
                                    TextEntry::make('country_value')->label('')
                                        ->state(fn ($record) =>
                                            $record->country
                                                ? "{$record->country->alpha_3} - {$record->country->name}"
                                                : '—'
                                        )
                                        ->columnSpan(9),
                                ]),

                            // Manager
                            InfoGrid::make(12)
                                ->extraAttributes(['style' => 'border-bottom:1px solid rgba(255,255,255,0.12); padding:2px 0;'])
                                ->schema([
                                    TextEntry::make('manager_label')->label('')
                                        ->state('Manager:')->weight('bold')->alignment('right')
                                        ->columnSpan(3),
                                    TextEntry::make('manager_value')->label('')
                                        ->state(fn ($record) => $record->manager?->name ?: '—')
                                        ->columnSpan(9),
                                ]),

                            // Operative Status
                            InfoGrid::make(12)
                                ->extraAttributes(['style' => 'border-bottom:1px solid rgba(255,255,255,0.12); padding:2px 0;'])
                                ->schema([
                                    TextEntry::make('op_status_label')->label('')
                                        ->state('Operative Status:')->weight('bold')->alignment('right')
                                        ->columnSpan(3),
                                    TextEntry::make('op_status_value')->label('')
                                        ->state(fn ($record) =>
                                            $record->operative_status
                                                ? "{$record->operative_status->acronym} - {$record->operative_status->description}"
                                                : '—'
                                        )
                                        ->columnSpan(9),
                                ]),

                            // LSK
                            InfoGrid::make(12)
                                ->extraAttributes(['style' => 'border-bottom:1px solid rgba(255,255,255,0.12); padding:2px 0;'])
                                ->schema([
                                    TextEntry::make('lsk_label')->label('')
                                        ->state('LSK:')->weight('bold')->alignment('right')
                                        ->columnSpan(3),
                                    TextEntry::make('lsk_value')->label('')
                                        ->state(fn ($record) => $record->cns_reinsurer ?: '—')
                                        ->columnSpan(9),
                                ]),
                        ]),

                    /* ── Col 3: Branding (logo arriba / icon abajo, mismo alto) ── */
                            InfoGrid::make(1)
                                ->columnSpan(1)
                                ->extraAttributes(['style' => 'display:flex;flex-direction:column;gap:12px;height:100%;'])
                                ->schema([

                                    // LOGO
                                    ImageEntry::make('logo_img')
                                        ->label('Logo')
                                        ->disk('s3')
                                        ->visibility('public')
                                        ->image()
                                        ->getStateUsing(fn ($record) => $record->logo)
                                        ->hidden(fn ($record) => blank($record->logo))
                                        ->extraAttributes([
                                            'style' => '
                                                flex:1 1 0; min-height:240px; width:100%;
                                                border-radius:14px;
                                                background:linear-gradient(135deg, rgba(255,255,255,0.08), rgba(255,255,255,0.03));
                                                border:1px solid rgba(255,255,255,0.15);
                                                display:flex; align-items:center; justify-content:center;
                                                padding:16px; margin:0; overflow:hidden;
                                            ',
                                        ])
                                        ->extraImgAttributes([
                                            'style' => 'max-width:100%; max-height:100%; object-fit:contain; display:block;',
                                        ]),

                                    TextEntry::make('logo_placeholder')
                                        ->label('Logo')
                                        ->weight('bold')
                                        ->html()
                                        ->state('
                                            <div style="
                                                flex:1 1 0; min-height:240px; width:100%;
                                                border-radius:14px;
                                                display:flex; align-items:center; justify-content:center;
                                                margin:0;
                                                border:1px dashed rgba(255,255,255,0.25); opacity:.7;
                                                background:linear-gradient(135deg, rgba(255,255,255,0.04), rgba(255,255,255,0.02));
                                            ">No logo</div>
                                        ')
                                        ->visible(fn ($record) => blank($record->logo))
                                        ->extraAttributes(['style' => 'margin:0; padding:0;']),

                                    // ICON
                                    ImageEntry::make('icon_img')
                                        ->label('Icon')
                                        ->disk('s3')
                                        ->visibility('public')
                                        ->image()
                                        ->getStateUsing(fn ($record) => $record->icon)
                                        ->hidden(fn ($record) => blank($record->icon))
                                        ->extraAttributes([
                                            'style' => '
                                                flex:1 1 0; min-height:240px; width:100%;
                                                border-radius:14px;
                                                background:linear-gradient(135deg, rgba(255,255,255,0.08), rgba(255,255,255,0.03));
                                                border:1px solid rgba(255,255,255,0.15);
                                                display:flex; align-items:center; justify-content:center;
                                                padding:16px; margin:0; overflow:hidden;
                                            ',
                                        ])
                                        ->extraImgAttributes([
                                            'style' => 'max-width:100%; max-height:100%; object-fit:contain; display:block;',
                                        ]),

                                    TextEntry::make('icon_fallback')
                                        ->label('Icon')
                                        ->weight('bold')
                                        ->html()
                                        ->state(function ($record) {
                                            $seed = trim($record->acronym ?: $record->short_name ?: $record->name ?: 'R');
                                            $initials = (mb_strlen($seed) === 3 && preg_match('/^[A-Z]{3}$/', $seed))
                                                ? $seed
                                                : collect(preg_split('/\s+/u', $seed))
                                                    ->filter()
                                                    ->map(fn ($w) => mb_strtoupper(mb_substr($w, 0, 1)))
                                                    ->take(2)
                                                    ->implode('');

                                            return "<div style=\"
                                                flex:1 1 0; min-height:240px; width:100%;
                                                border-radius:14px;
                                                display:flex; align-items:center; justify-content:center;
                                                margin:0;
                                                background:linear-gradient(135deg, rgba(255,255,255,0.08), rgba(255,255,255,0.03));
                                                border:1px solid rgba(255,255,255,0.15);
                                                font-weight:700; font-size:28px;
                                            \">{$initials}</div>";
                                        })
                                        ->visible(fn ($record) => blank($record->icon))
                                        ->extraAttributes(['style' => 'margin:0; padding:0;']),
                                ]),
                    ]),
            ])
            ->maxWidth('8xl')
            ->collapsible(),
        ]);
    }
















    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
                TextColumn::make('id')->sortable()
                    ->extraAttributes([
                        'style' => 'width: 30px; white-space: normal;', // ✅ Deja que el texto se envuelva
                    ]),

                TextColumn::make('cns_reinsurer')->sortable()
                    ->label('Lsk')
                    ->extraAttributes([
                        'style' => 'width: 30px; white-space: normal;', // ✅ Deja que el texto se envuelva
                    ]),
                
                TextColumn::make('short_name')
                    ->label('Name')
                    ->html()
                    ->formatStateUsing(function ($state, $record) {
                        $iconPath  = $record->icon;
                        $shortName = $record->short_name;

                        if (blank($iconPath)) {
                            return "<span>{$shortName}</span>";
                        }

                        $iconUrl = Str::startsWith($iconPath, ['http://', 'https://'])
                            ? $iconPath
                            : rtrim(config('filesystems.disks.s3.url'), '/') . '/' . ltrim($iconPath, '/');

                        return "<div style='display:flex;align-items:center;gap:8px;'>
                                    <img src='{$iconUrl}'
                                        alt='icon'
                                        style='width:24px;height:24px;border-radius:50%;object-fit:cover;' />
                                    <span>{$shortName}</span>
                                </div>";
                    })
                    ->sortable()
                    ->searchable(),

                
                TextColumn::make('acronym')
                    ->searchable()
                    ->sortable(),   
                TextColumn::make('established')
                    ->searchable()
                    ->sortable(), 
                TextColumn::make('class')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('parent.short_name')
                    ->label('Parent')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('reinsurer_type.description')
                    ->label('Type')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('country.alpha_3')
                    ->label('Country')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('operative_status.description')
                    ->label('Operative Status')
                    ->searchable()
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Operative' => 'success',
                        'Pending license' => 'warning',
                        'Pending incop.' => 'warning',
                        'Transferred' => 'info',
                        'Dissolved' => 'danger',
                        'Run-off' => 'gray',
                        'Dormant' => 'gray',
                    default => 'secondary',
                    })
                    ->icon(fn (string $state): string => match ($state) {
                        'Operative' => 'heroicon-o-check-circle',
                        'Pending license', 'Pending incop.' => 'heroicon-o-clock',
                        'Transferred' => 'heroicon-o-arrow-right-circle',
                        'Dissolved' => 'heroicon-o-x-circle',
                        'Run-off' => 'heroicon-o-pause-circle',
                        'Dormant' => 'heroicon-o-moon',
                        default => 'heroicon-o-question-mark-circle',
                    })
                    ->sortable()
                    ->searchable(),
                
    
    
            ])
            ->filters([

                 // ✅ Filtro por la columna Country
                SelectFilter::make('country_id')
                ->label('Country')
                ->options(function () {
                    return Country::whereIn('id', Reinsurer::select('country_id'))
                        ->orderBy('name')
                        ->pluck('name', 'id'); // 'id' como key, 'name' como etiqueta
                })
                ->searchable()
                ->indicator('Country'),


                // ✅ Filtro por la columna Operative Status
                SelectFilter::make('operative_status_id')
                ->label('Operative Status')
                ->options(function () {
                    return OperativeStatus::whereIn('id', 
                        Reinsurer::distinct()->pluck('operative_status_id')
                    )->pluck('description', 'id');
                })
                ->searchable()
                ->indicator('Status'),

                // ✅ Filtro por la columna Type
                SelectFilter::make('reinsurer_type_id')
                ->label('Type')
                ->options(function () {
                    return ReinsurerType::whereIn('id', 
                        Reinsurer::distinct()->pluck('reinsurer_type_id')
                    )->pluck('description', 'id');
                })
                ->searchable()
                ->indicator('Type'),

                 // ✅ Filtro por la columna Class
                SelectFilter::make('class')
                ->label('Class')
                ->options([
                    'Class 1' => 'Class 1',
                    'Class 2' => 'Class 2',
                ])
                ->searchable()
                ->indicator('Class'),

            ])




            ->headerActions([
                        Action::make('export')
                            ->label('Export to Excel')
                            ->icon('heroicon-o-arrow-down-tray')
                            ->color('primary')
                            ->action(function () {
                                $records = \App\Models\Reinsurer::query()
                                    ->with(['parent', 'manager', 'country', 'reinsurer_type', 'operative_status'])
                                    ->get();

                                if ($records->isEmpty()) {
                                    Notification::make()->title('No reinsurers found.')->info()->send();
                                    return;
                                }

                                $filename = sprintf(
                                    'Reinsurers_%s.xlsx',
                                    Carbon::now()->format('Ymd_His')
                                );

                                return Excel::download(new ReinsurersExport($records), $filename);
                            }),
                    ])











            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make()
                    ->label('View')
                    ->url(fn (Reinsurer $record) =>
                        self::getUrl('view', ['record' => $record])
                    )
                    ->icon('heroicon-m-eye'),  // opcional 

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
            RelationManagers\DocumentsRelationManager::class,   // ✅ nombre correcto
            RelationManagers\ReinsurerBankAccountsRelationManager::class,   // ✅ nombre correcto
            RelationManagers\FinancialStatementsRelationManager::class,   // ✅ nombre correcto
            RelationManagers\ReinsurerHoldingsRelationManager::class,   // ✅ nombre correcto
            RelationManagers\BoardsRelationManager::class,   // ✅ nombre correcto
            

            
        ];
    }


    public static function getPages(): array
    {
        return [
             'index'  => Pages\ListReinsurers::route('/'),
             'create' => Pages\CreateReinsurers::route('/create'),
             'view'   => Pages\ViewReinsurer::route('/{record}'), // 👈  NUEVO
             'edit'   => Pages\EditReinsurers::route('/{record}/edit'),
        ];
    }
}
