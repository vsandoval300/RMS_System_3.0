<?php

namespace App\Filament\Resources\Reinsurers;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\ViewAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Resources\Reinsurers\RelationManagers\DocumentsRelationManager;
use App\Filament\Resources\Reinsurers\RelationManagers\ReinsurerBankAccountsRelationManager;
use App\Filament\Resources\Reinsurers\RelationManagers\FinancialStatementsRelationManager;
use App\Filament\Resources\Reinsurers\RelationManagers\ReinsurerHoldingsRelationManager;
use App\Filament\Resources\Reinsurers\RelationManagers\BoardsRelationManager;
use App\Filament\Resources\Reinsurers\Pages\ListReinsurers;
use App\Filament\Resources\Reinsurers\Pages\CreateReinsurers;
use App\Filament\Resources\Reinsurers\Pages\ViewReinsurer;
use App\Filament\Resources\Reinsurers\Pages\EditReinsurers;
use App\Filament\Resources\ReinsurersResource\Pages;
use App\Filament\Resources\ReinsurersResource\RelationManagers;
use App\Models\Country;
use App\Models\Reinsurer;
use App\Models\OperativeStatus;
use App\Models\ReinsurerType;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Components\Html;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile; // Livewire v3
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DatePicker;
use Maatwebsite\Excel\Facades\Excel;
use Filament\Notifications\Notification;
use App\Exports\ReinsurersExport;
use Carbon\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;



class ReinsurersResource extends Resource
{
    protected static ?string $model = Reinsurer::class;
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-minus';
    protected static string | \UnitEnum | null $navigationGroup = 'Reinsurers';
    protected static ?string $icon = 'heroicon-o-building-office';
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




    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
            
                // ========== DETAILS ==========
                Section::make('Details')
                    ->columnSpan('full')        
                    ->columns(12)
                    ->schema([

                        Section::make() // 👈 sin título
                            ->columns(6)
                            ->columnSpan('full')  
                            ->schema([
                                TextInput::make('id')
                                    ->label('ID')
                                    ->default(fn ($record) => $record?->id ?? '(pending)')
                                    ->readOnly()
                                    ->disabled(),

                                TextInput::make('cns_reinsurer')
                                    ->label('LSK (Legacy Substitute Key)')
                                    ->placeholder("Please provide LSK number if exist.")
                                    ->unique(
                                        ignoreRecord: true,
                                        modifyRuleUsing: fn (Unique $rule) => $rule->whereNull('deleted_at')
                                    )
                                    ->nullable()
                                    ->columnSpan(2),
                            ])
                            ->compact(),
                     ]),



                        Section::make('Basic Info')
                            ->columns(6)
                            ->columnSpan('full')  
                            ->schema([
                                TextInput::make('name')
                                    ->label('Name')
                                    ->placeholder("Please provide reinsurer's name")
                                    ->required()
                                    ->unique(
                                        ignoreRecord: true,
                                        modifyRuleUsing: fn (Unique $rule) => $rule->whereNull('deleted_at')
                                    )
                                    /* ->rules([
                                        Rule::unique('reinsurers', 'name')
                                            ->whereNull('deleted_at'),
                                    ]) */
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
                                    /* ->rules([
                                        Rule::unique('reinsurers', 'short_name')
                                            ->whereNull('deleted_at'),
                                    ]) */
                                    ->required()
                                    ->unique(
                                        ignoreRecord: true,
                                        modifyRuleUsing: fn (Unique $rule) => $rule->whereNull('deleted_at')
                                    )
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
                                   ->unique(
                                        ignoreRecord: true,
                                        modifyRuleUsing: fn (Unique $rule) => $rule->whereNull('deleted_at')
                                    )
                                    /* ->rules([
                                        Rule::unique('reinsurers', 'short_name')
                                            ->whereNull('deleted_at'),
                                    ]) */
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
                            ->columnSpanFull()
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
                    ->columnSpanFull()
                    ->schema([
                        Section::make('Logo Upload')
                            ->columnSpan(1)
                            ->schema([
                                Html::make(function ($record) {
                                        if (blank($record?->logo)) {
                                            return new \Illuminate\Support\HtmlString(
                                                '<p style="color:#9ca3af;font-size:0.8rem;margin:0;">No logo uploaded yet.</p>'
                                            );
                                        }
                                        $_path = $record->logo;
                                        if (str_starts_with($_path, 'http://') || str_starts_with($_path, 'https://')) {
                                            $url = $_path;
                                        } else {
                                            /** @var \Illuminate\Contracts\Filesystem\Cloud $s3 */
                                            $s3  = Storage::disk('s3');
                                            $url = $s3->url($_path);
                                        }
                                        $filename = basename($_path);
                                        $relPath  = str_starts_with($_path, 'http') ? ltrim(parse_url($_path, PHP_URL_PATH), '/') : $_path;
                                        try {
                                            /** @var \Illuminate\Filesystem\FilesystemAdapter $s3dl */
                                            $s3dl  = Storage::disk('s3');
                                            $dlUrl = $s3dl->temporaryUrl($relPath, now()->addMinutes(30), ['ResponseContentDisposition' => 'attachment; filename="' . addslashes($filename) . '"']);
                                        } catch (\Throwable $e) {
                                            $dlUrl = $url;
                                        }
                                        $html  = '<div style="display:flex;flex-direction:column;align-items:center;';
                                        $html .= 'gap:10px;padding:16px;border-radius:10px;';
                                        $html .= 'background:rgba(0,0,0,0.03);border:1px solid rgba(0,0,0,0.1);">';
                                        $html .= '<img src="' . e($url) . '" alt="Logo"';
                                        $html .= ' style="max-height:160px;max-width:100%;object-fit:contain;border-radius:6px;" />';
                                        $html .= '<div style="display:flex;gap:8px;">';
                                        $html .= '<a href="' . e($dlUrl) . '"';
                                        $html .= ' style="font-size:0.78rem;color:#34d399;text-decoration:none;';
                                        $html .= 'padding:4px 12px;border-radius:6px;border:1px solid rgba(52,211,153,0.4);">Download</a>';
                                        $html .= '</div>';
                                        $html .= '<code style="font-size:0.7rem;color:#6b7280;">' . e($filename) . '</code>';
                                        $html .= '</div>';
                                        return new \Illuminate\Support\HtmlString($html);
                                    }),
                                FileUpload::make('logo')
                                    ->label('Reinsurer Logo')
                                    ->disk('s3')
                                    ->directory('reinsurers/logos')
                                    ->visibility('public')
                                    ->image()
                                    ->acceptedFileTypes(['image/png', 'image/jpeg', 'image/svg+xml'])
                                    ->preserveFilenames()
                                    ->downloadable()
                                    ->openable()
                                    ->helperText('Upload the reinsurer’s logo (PNG, JPG, or SVG, preferably square).')

                                    // 👇 CLAVE: si el estado viene null, conservar el valor que ya tenía el registro
                                    ->dehydrateStateUsing(function ($state, ?Reinsurer $record) {
                                        // 1) Si no se eligió nada, conserva el valor que ya tenía el registro
                                        if (blank($state)) {
                                            return $record?->logo;
                                        }

                                        // 2) Si viene como ["uuid" => "ruta"], nos quedamos SOLO con la ruta
                                        if (is_array($state)) {
                                            // ejemplo: ["tbd...uuid..." => "reinsurers/logos/55-Logoprueba.png"]
                                            $state = array_values($state)[0] ?? null;
                                        }

                                        // 3) Aquí ya es string (o null)
                                        return $state;
                                    })

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
                                Html::make(function ($record) {
                                        if (blank($record?->icon)) {
                                            return new \Illuminate\Support\HtmlString(
                                                '<p style="color:#9ca3af;font-size:0.8rem;margin:0;">No icon uploaded yet.</p>'
                                            );
                                        }
                                        $_path = $record->icon;
                                        if (str_starts_with($_path, 'http://') || str_starts_with($_path, 'https://')) {
                                            $url = $_path;
                                        } else {
                                            /** @var \Illuminate\Contracts\Filesystem\Cloud $s3 */
                                            $s3  = Storage::disk('s3');
                                            $url = $s3->url($_path);
                                        }
                                        $filename = basename($_path);
                                        $relPath  = str_starts_with($_path, 'http') ? ltrim(parse_url($_path, PHP_URL_PATH), '/') : $_path;
                                        try {
                                            /** @var \Illuminate\Filesystem\FilesystemAdapter $s3dl */
                                            $s3dl  = Storage::disk('s3');
                                            $dlUrl = $s3dl->temporaryUrl($relPath, now()->addMinutes(30), ['ResponseContentDisposition' => 'attachment; filename="' . addslashes($filename) . '"']);
                                        } catch (\Throwable $e) {
                                            $dlUrl = $url;
                                        }
                                        $html  = '<div style="display:flex;flex-direction:column;align-items:center;';
                                        $html .= 'gap:10px;padding:16px;border-radius:10px;';
                                        $html .= 'background:rgba(0,0,0,0.03);border:1px solid rgba(0,0,0,0.1);">';
                                        $html .= '<img src="' . e($url) . '" alt="Icon"';
                                        $html .= ' style="max-height:160px;max-width:100%;object-fit:contain;border-radius:6px;" />';
                                        $html .= '<div style="display:flex;gap:8px;">';
                                        $html .= '<a href="' . e($dlUrl) . '"';
                                        $html .= ' style="font-size:0.78rem;color:#34d399;text-decoration:none;';
                                        $html .= 'padding:4px 12px;border-radius:6px;border:1px solid rgba(52,211,153,0.4);">Download</a>';
                                        $html .= '</div>';
                                        $html .= '<code style="font-size:0.7rem;color:#6b7280;">' . e($filename) . '</code>';
                                        $html .= '</div>';
                                        return new \Illuminate\Support\HtmlString($html);
                                    }),
                                FileUpload::make('icon')
                                    ->label('Reinsurer Icon')
                                    ->disk('s3')
                                    ->directory('reinsurers/icons')
                                    ->visibility('public')
                                    ->image()
                                    ->acceptedFileTypes(['image/png', 'image/jpeg', 'image/svg+xml'])
                                    ->preserveFilenames()
                                    ->downloadable()
                                    ->openable()
                                    ->helperText('Upload the reinsurer’s icon (PNG, JPG, or SVG, preferably square).')

                                    ->dehydrateStateUsing(function ($state, ?Reinsurer $record) {
                                        if (blank($state)) {
                                            return $record?->icon;
                                        }

                                        if (is_array($state)) {
                                            $state = array_values($state)[0] ?? null;
                                        }

                                        return $state;
                                    })

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

    




public static function infolist(Schema $schema): Schema
{
    return $schema->components([
        Section::make('Reinsurer Profile')
        ->columnSpan('full')
        ->schema([
            Grid::make(3)
                ->extraAttributes(['style' => 'gap: 6px;'])
                ->schema([

                    /* ── Cols 1–2: filas compactas "Label + Value" ── */
                    Section::make()->compact()
                        ->columnSpan(2)
                        ->schema([
                            Grid::make(1)
                                ->extraAttributes(['style' => 'row-gap: 0;'])
                                ->schema([

                            // Name
                            Grid::make(12)
                                ->extraAttributes(['style' => 'border-bottom:1px solid rgba(255,255,255,0.12); padding:2px 0;'])
                                ->schema([
                                    TextEntry::make('name_label')->hiddenLabel()
                                        ->state('Name:')->weight('bold')->alignment('right')
                                        ->columnSpan(3),
                                    TextEntry::make('name_value')->hiddenLabel()
                                        ->state(fn ($record) => $record->name ?: '—')
                                        ->columnSpan(9),
                                ]),

                            // Short Name
                            Grid::make(12)
                                ->extraAttributes(['style' => 'border-bottom:1px solid rgba(255,255,255,0.12); padding:2px 0;'])
                                ->schema([
                                    TextEntry::make('short_label')->hiddenLabel()
                                        ->state('Short Name:')->weight('bold')->alignment('right')
                                        ->columnSpan(3),
                                    TextEntry::make('short_value')->hiddenLabel()
                                        ->state(fn ($record) => $record->short_name ?: '—')
                                        ->columnSpan(9),
                                ]),

                            // Acronym
                            Grid::make(12)
                                ->extraAttributes(['style' => 'border-bottom:1px solid rgba(255,255,255,0.12); padding:2px 0;'])
                                ->schema([
                                    TextEntry::make('acr_label')->hiddenLabel()
                                        ->state('Acronym:')->weight('bold')->alignment('right')
                                        ->columnSpan(3),
                                    TextEntry::make('acr_value')->hiddenLabel()
                                        ->state(fn ($record) => $record->acronym ?: '—')
                                        ->columnSpan(9),
                                ]),

                            // Type
                            Grid::make(12)
                                ->extraAttributes(['style' => 'border-bottom:1px solid rgba(255,255,255,0.12); padding:2px 0;'])
                                ->schema([
                                    TextEntry::make('type_label')->hiddenLabel()
                                        ->state('Type:')->weight('bold')->alignment('right')
                                        ->columnSpan(3),
                                    TextEntry::make('type_value')->hiddenLabel()
                                        ->state(fn ($record) =>
                                            $record->reinsurer_type
                                                ? "{$record->reinsurer_type->type_acronym} - {$record->reinsurer_type->description}"
                                                : '—'
                                        )
                                        ->columnSpan(9),
                                ]),

                            // Class
                            Grid::make(12)
                                ->extraAttributes(['style' => 'border-bottom:1px solid rgba(255,255,255,0.12); padding:2px 0;'])
                                ->schema([
                                    TextEntry::make('class_label')->hiddenLabel()
                                        ->state('Class:')->weight('bold')->alignment('right')
                                        ->columnSpan(3),
                                    TextEntry::make('class_value')->hiddenLabel()
                                        ->state(fn ($record) => $record->class ?: '—')
                                        ->columnSpan(9),
                                ]),

                            // Parent
                            Grid::make(12)
                                ->extraAttributes(['style' => 'border-bottom:1px solid rgba(255,255,255,0.12); padding:2px 0;'])
                                ->schema([
                                    TextEntry::make('parent_label')->hiddenLabel()
                                        ->state('Parent:')->weight('bold')->alignment('right')
                                        ->columnSpan(3),
                                    TextEntry::make('parent_value')->hiddenLabel()
                                        ->state(fn ($record) => $record->parent?->name ?: '—')
                                        ->columnSpan(9),
                                ]),

                            // Established
                            Grid::make(12)
                                ->extraAttributes(['style' => 'border-bottom:1px solid rgba(255,255,255,0.12); padding:2px 0;'])
                                ->schema([
                                    TextEntry::make('est_label')->hiddenLabel()
                                        ->state('Established:')->weight('bold')->alignment('right')
                                        ->columnSpan(3),
                                    TextEntry::make('est_value')->hiddenLabel()
                                        ->state(fn ($record) => $record->established ?: '—')
                                        ->columnSpan(9),
                                ]),

                            // Country
                            Grid::make(12)
                                ->extraAttributes(['style' => 'border-bottom:1px solid rgba(255,255,255,0.12); padding:2px 0;'])
                                ->schema([
                                    TextEntry::make('country_label')->hiddenLabel()
                                        ->state('Country:')->weight('bold')->alignment('right')
                                        ->columnSpan(3),
                                    TextEntry::make('country_value')->hiddenLabel()
                                        ->state(fn ($record) =>
                                            $record->country
                                                ? "{$record->country->alpha_3} - {$record->country->name}"
                                                : '—'
                                        )
                                        ->columnSpan(9),
                                ]),

                            // Manager
                            Grid::make(12)
                                ->extraAttributes(['style' => 'border-bottom:1px solid rgba(255,255,255,0.12); padding:2px 0;'])
                                ->schema([
                                    TextEntry::make('manager_label')->hiddenLabel()
                                        ->state('Manager:')->weight('bold')->alignment('right')
                                        ->columnSpan(3),
                                    TextEntry::make('manager_value')->hiddenLabel()
                                        ->state(fn ($record) => $record->manager?->name ?: '—')
                                        ->columnSpan(9),
                                ]),

                            // Operative Status
                            Grid::make(12)
                                ->extraAttributes(['style' => 'border-bottom:1px solid rgba(255,255,255,0.12); padding:2px 0;'])
                                ->schema([
                                    TextEntry::make('op_status_label')->hiddenLabel()
                                        ->state('Operative Status:')->weight('bold')->alignment('right')
                                        ->columnSpan(3),
                                    TextEntry::make('op_status_value')->hiddenLabel()
                                        ->state(fn ($record) =>
                                            $record->operative_status
                                                ? "{$record->operative_status->acronym} - {$record->operative_status->description}"
                                                : '—'
                                        )
                                        ->columnSpan(9),
                                ]),

                            // LSK
                            Grid::make(12)
                                ->extraAttributes(['style' => 'border-bottom:1px solid rgba(255,255,255,0.12); padding:2px 0;'])
                                ->schema([
                                    TextEntry::make('lsk_label')->hiddenLabel()
                                        ->state('LSK:')->weight('bold')->alignment('right')
                                        ->columnSpan(3),
                                    TextEntry::make('lsk_value')->hiddenLabel()
                                        ->state(fn ($record) => $record->cns_reinsurer ?: '—')
                                        ->columnSpan(9),
                                ]),
                                ]),
                        ]),

                    /* ── Col 3: Branding (logo arriba / icon abajo, mismo alto) ── */
                            Grid::make(1)
                                ->columnSpan(1)
                                ->extraAttributes(['style' => 'display:flex;flex-direction:column;gap:12px;height:100%;'])
                                ->schema([

                                    // LOGO
                                    Section::make('Logo')->compact()->schema([
                                    ImageEntry::make('logo_img')
                                        ->label('Logo')
                                        ->disk('s3')
                                        ->visibility('public')
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
                                    ]),

                                    // ICON
                                    Section::make('Icon')->compact()->schema([
                                    ImageEntry::make('icon_img')
                                        ->label('Icon')
                                        ->disk('s3')
                                        ->visibility('public')
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
                    ]),
            ])
            ->maxWidth('9xl')
            ->collapsible(),
        ]);
    }





    public static function table(Table $table): Table
    {
        return $table
            ->recordUrl(fn (Reinsurer $record) => static::getUrl('view', ['record' => $record]))
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
                                $records = Reinsurer::query()
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

            ->recordActions([
                ActionGroup::make([
                    ViewAction::make()
                    ->label('View')
                    ->url(fn (Reinsurer $record) =>
                        self::getUrl('view', ['record' => $record])
                    )
                    ->icon('heroicon-m-eye'),  // opcional 

                    EditAction::make(),
                    DeleteAction::make(),
                ])

            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->paginationPageOptions([10, 25, 50, 100, 'all']);
    }

    public static function getRelations(): array
    {
        return [
            DocumentsRelationManager::class,   // ✅ nombre correcto
            ReinsurerBankAccountsRelationManager::class,   // ✅ nombre correcto
            FinancialStatementsRelationManager::class,   // ✅ nombre correcto
            ReinsurerHoldingsRelationManager::class,   // ✅ nombre correcto
            BoardsRelationManager::class,   // ✅ nombre correcto
            

            
        ];
    }


    public static function getPages(): array
    {
        return [
             'index'  => ListReinsurers::route('/'),
             'create' => CreateReinsurers::route('/create'),
             'view'   => ViewReinsurer::route('/{record}'), // 👈  NUEVO
             'edit'   => EditReinsurers::route('/{record}/edit'),
        ];
    }
}
