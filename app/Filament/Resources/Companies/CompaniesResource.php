<?php

namespace App\Filament\Resources\Companies;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Actions\ActionGroup;
use Filament\Actions\ViewAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Resources\Companies\Pages\ListCompanies;
use App\Filament\Resources\Companies\Pages\CreateCompanies;
use App\Filament\Resources\Companies\Pages\ViewCompanies;
use App\Filament\Resources\Companies\Pages\EditCompanies;
use App\Models\Company;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Html;
use Filament\Tables\Columns\TextColumn;
use App\Models\Country;
use Filament\Support\Enums\VerticalAlignment;
use Illuminate\Support\HtmlString;
use Illuminate\Validation\Rules\Unique;
use Filament\Infolists\Components\TextEntry;

class CompaniesResource extends Resource
{
    protected static ?string $model = Company::class;
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-minus';
    protected static string | \UnitEnum | null $navigationGroup = 'Underwritten';
    protected static ?int    $navigationSort  = 10;   // aparecerá primero

    /* ───── NUEVO: burbuja con el total en el menú ───── */
    public static function getNavigationBadge(): ?string
    {
        // Puedes usar self::$model::count() o Reinsurer::count()
        return Company::count();
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                //
                Section::make('Companies Details')
                ->columns(1)    // ← aquí defines dos columnas
                ->columnSpan('full')
                ->schema([
                    

                    TextInput::make('name')
                    ->label('Name')
                    ->required()
                    ->live(debounce: 500)
                    ->unique(
                        ignoreRecord: true,
                        modifyRuleUsing: fn (Unique $rule) => $rule->whereNull('deleted_at')
                    )
                    ->maxLength(255)
                    ->helperText('First letter of each word will be capitalised.'),

                    Html::make(fn ($get, $record): HtmlString =>
                        static::buildDuplicateWarning($get('name'), $record?->getKey())
                    )
                    ->visible(fn ($get, $record): bool =>
                        static::hasSimilarCompanies($get('name'), $record?->getKey())
                    ),

                    TextInput::make('acronym')
                    ->label('Acronym')
                    ->required()
                    ->unique(
                        ignoreRecord: true,
                        modifyRuleUsing: fn (Unique $rule) => $rule->whereNull('deleted_at')
                    )
                    //->live(onBlur: false)
                    ->maxLength(255)
                    ->rule('regex:/^[A-Z0-9_]+$/')
                    ->afterStateUpdated(fn ($state, callable $set) => $set('acronym', strtoupper($state)))
                    ->helperText('Only uppercase letters allowed.'),
                    //->extraAttributes(['class' => 'w-1/2']),

                    Textarea::make('activity')
                    ->label('Activity')
                    ->required()
                    ->live(debounce: 600)
                    ->columnSpan('full')
                    ->autosize()
                    ->hint(fn ($get): ?string => static::languageHint($get('activity')))
                    ->hintColor('warning')
                    ->rule(fn () => function (string $attribute, mixed $value, \Closure $fail) {
                        if (static::isNonEnglish($value)) {
                            $fail('The Activity description must be written in English. Non-English text was detected.');
                        }
                    })
                    ->helperText('Please provide a brief description of the sector in English. Only the first letter will be capitalised.'),
                    //->extraAttributes(['class' => 'w-1/2']),

                    Select::make('country_id')
                            ->label(__('Country'))
                            ->options(function () {
                                return Country::orderBy('name')
                                    ->get()
                                    ->mapWithKeys(fn ($country) => [
                                        $country->id => "{$country->alpha_3} - {$country->name}"
                                    ]);
                            })
                            ->searchable()
                            ->preload()
                            ->optionsLimit(300)
                            ->placeholder('Choose the reinsurer\'s country')
                            ->required()
                            ->placeholder('Select a country'),
                            //->helperText('Choose the reinsurer\'s country.'),

                    Select::make('industry_id')
                        ->label('Industry')
                        ->relationship('sector','name')
                        ->searchable()
                        ->preload()
                        ->required(),
                        //->extraAttributes(['class' => 'w-1/2']),

                ])
                ->maxWidth('5xl')
                ->collapsible(),

            ]);
    }






    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            /* ─────────────────────────  PROFILE  ───────────────────────── */
            Section::make('Company Profile')
            ->columnSpanFull()
            ->schema([
                Grid::make(2)
                    ->extraAttributes(['style' => 'gap: 6px;'])
                    ->schema([

                        // Cols 1–2: filas “Label (3) + Value (9)”
                        Grid::make(1)
                            ->columnSpan(2)
                            ->extraAttributes(['style' => 'row-gap: 0;'])
                            ->schema([

                                // Name
                                Grid::make(12)
                                    ->extraAttributes(['style' => 'border-bottom:1px solid rgba(255,255,255,0.12); padding:2px 0;'])
                                    ->schema([
                                        TextEntry::make('name_label')->hiddenLabel()->state('Name:')
                                            ->weight('bold')->alignment('right')->columnSpan(3),
                                        TextEntry::make('name_value')->hiddenLabel()
                                            ->state(fn ($record) => $record->name ?: '—')
                                            ->columnSpan(9),
                                    ]),

                                // Acronym
                                Grid::make(12)
                                    ->extraAttributes(['style' => 'border-bottom:1px solid rgba(255,255,255,0.12); padding:2px 0;'])
                                    ->schema([
                                        TextEntry::make('acr_label')->hiddenLabel()->state('Acronym:')
                                            ->weight('bold')->alignment('right')->columnSpan(3),
                                        TextEntry::make('acr_value')->hiddenLabel()
                                            ->state(fn ($record) => $record->acronym ?: '—')
                                            ->columnSpan(9),
                                    ]),

                                // Activity (multilínea)
                                Grid::make(12)
                                    ->extraAttributes(['style' => 'border-bottom:1px solid rgba(255,255,255,0.12); padding:2px 0;'])
                                    ->schema([
                                        TextEntry::make('act_label')->hiddenLabel()->state('Activity:')
                                            ->weight('bold')->alignment('right')->columnSpan(3),
                                        TextEntry::make('act_value')->hiddenLabel()
                                            ->state(fn ($record) => $record->activity ?: '—')
                                            ->extraAttributes(['style' => 'line-height:1.35;'])
                                            ->columnSpan(9),
                                    ]),

                                // Country
                                Grid::make(12)
                                    ->extraAttributes(['style' => 'border-bottom:1px solid rgba(255,255,255,0.12); padding:2px 0;'])
                                    ->schema([
                                        TextEntry::make('country_label')->hiddenLabel()->state('Country:')
                                            ->weight('bold')->alignment('right')->columnSpan(3),
                                        TextEntry::make('country_value')->hiddenLabel()
                                            ->state(fn ($record) =>
                                                $record->country
                                                    ? ($record->country->alpha_3 ?? '') . (isset($record->country->alpha_3) ? ' - ' : '') . $record->country->name
                                                    : '—'
                                            )
                                            ->columnSpan(9),
                                    ]),

                                // Sector
                                Grid::make(12)
                                    ->extraAttributes(['style' => 'border-bottom:1px solid rgba(255,255,255,0.12); padding:2px 0;'])
                                    ->schema([
                                        TextEntry::make('sector_label')->hiddenLabel()->state('Sector:')
                                            ->weight('bold')->alignment('right')->columnSpan(3),
                                        TextEntry::make('sector_value')->hiddenLabel()
                                            ->state(fn ($record) => $record->sector?->name ?: '—')
                                            ->columnSpan(9),
                                    ]),
                            ]),
                    ]),
            ])
            ->maxWidth('5xl')
            ->collapsible(),

           
        ]);
    }





    public static function table(Table $table): Table
    {
        return $table
            ->recordUrl(fn (Company $record) => static::getUrl('view', ['record' => $record]))
            ->columns([
                //
                TextColumn::make('id')
                    ->verticalAlignment(VerticalAlignment::Start)
                    ->sortable(),
                TextColumn::make('name')
                    ->verticalAlignment(VerticalAlignment::Start)
                    ->searchable()
                    ->sortable()
                    ->extraAttributes([
                        'style' => 'width: 320px; white-space: normal;', // ✅ Deja que el texto se envuelva
                    ]),
                TextColumn::make('acronym')
                    ->verticalAlignment(VerticalAlignment::Start)
                    ->searchable()
                    ->sortable(),
                TextColumn::make('activity')
                    ->verticalAlignment(VerticalAlignment::Start)
                    ->label('Activity')
                    ->sortable()
                    ->searchable()
                    ->wrap()
                    ->extraAttributes([
                        'style' => 'width: 550px; white-space: normal;', // ancho fijo de 300px
                    ]),
                TextColumn::make('sector.name')
                    ->verticalAlignment(VerticalAlignment::Start)
                    ->label('Sector')
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('country.name')
                    ->verticalAlignment(VerticalAlignment::Start)
                    ->label('Country')
                    ->sortable()
                    ->searchable()
                    ->extraAttributes([
                        'style' => 'width: 250px; white-space: normal;', // ancho fijo de 300px
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
            'index' => ListCompanies::route('/'),
            'create' => CreateCompanies::route('/create'),
            'view'   => ViewCompanies::route('/{record}'),
            'edit' => EditCompanies::route('/{record}/edit'),
        ];
    }

    // ── Duplicate detection ───────────────────────────────────────────────────

    private static function querySimilarCompanies(?string $name, mixed $excludeId = null): \Illuminate\Database\Eloquent\Collection
    {
        if (blank($name) || mb_strlen(trim($name)) < 3) return new \Illuminate\Database\Eloquent\Collection();

        $query = Company::where('name', 'LIKE', '%' . $name . '%')
            ->whereNull('deleted_at')
            ->with('country:id,name,alpha_3');

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->limit(5)->get(['id', 'name', 'acronym', 'country_id']);
    }

    public static function hasSimilarCompanies(?string $name, mixed $excludeId = null): bool
    {
        return static::querySimilarCompanies($name, $excludeId)->isNotEmpty();
    }

    public static function buildDuplicateWarning(?string $name, mixed $excludeId = null): HtmlString
    {
        $similar = static::querySimilarCompanies($name, $excludeId);

        if ($similar->isEmpty()) return new HtmlString('');

        $rows = $similar->map(fn ($c) =>
            '<li style="margin:0.25rem 0; color:light-dark(#78350f,#fde68a);">'
            . '<strong>' . e($c->name) . '</strong>'
            . ($c->acronym ? ' <span style="opacity:.7;">(' . e($c->acronym) . ')</span>' : '')
            . ($c->country ? ' &nbsp;·&nbsp; ' . e($c->country->alpha_3 . ' – ' . $c->country->name) : '')
            . '</li>'
        )->join('');

        $count  = $similar->count();
        $plural = $count > 1 ? 's' : '';

        $html = '
            <div x-data="{ open: true }" x-show="open" style="position:relative;">
                <div style="
                    background: light-dark(#fef9c3,#1c1908);
                    border: 1px solid light-dark(#fde047,#92400e);
                    border-left: 4px solid light-dark(#f59e0b,#fbbf24);
                    border-radius: 0.5rem;
                    padding: 0.75rem 2.5rem 0.75rem 1rem;
                    font-size: 0.82rem;
                    position: relative;
                ">
                    <button
                        type="button"
                        @click="open = false"
                        title="Dismiss"
                        style="
                            position:absolute; top:0.45rem; right:0.6rem;
                            background:none; border:none; cursor:pointer;
                            color:light-dark(#92400e,#fbbf24);
                            font-size:1rem; line-height:1; padding:0.2rem 0.35rem;
                            border-radius:0.25rem; opacity:0.65;
                        "
                        onmouseover="this.style.opacity=1"
                        onmouseout="this.style.opacity=0.65"
                    >✕</button>
                    <div style="font-weight:700; color:light-dark(#92400e,#fbbf24); margin-bottom:0.35rem; display:flex; align-items:center; gap:0.4rem;">
                        <svg style="width:15px;height:15px;flex-shrink:0;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
                        </svg>
                        Potential duplicate' . $plural . ' found (' . $count . ')
                    </div>
                    <div style="color:light-dark(#92400e,#fcd34d); margin-bottom:0.4rem;">
                        The following existing companies have similar names. Please verify before saving:
                    </div>
                    <ul style="margin:0; padding-left:1.1rem;">
                        ' . $rows . '
                    </ul>
                    <div style="margin-top:0.5rem; font-size:0.75rem; color:light-dark(#a16207,#fbbf24); border-top:1px solid light-dark(#fde047,#92400e); padding-top:0.4rem;">
                        You can still proceed if this is a different company.
                    </div>
                </div>
            </div>';

        return new HtmlString($html);
    }

    // ── Language validation ───────────────────────────────────────────────────

    public static function isNonEnglish(?string $text): bool
    {
        if (blank($text) || mb_strlen(trim($text)) < 4) return false;

        // Layer 1: any non-ASCII letter → strong signal (accents, Cyrillic, CJK, Arabic, etc.)
        if (preg_match('/\p{L}&&[^\x00-\x7F]/u', $text)) return true;
        // Simpler equivalent for non-ASCII detection
        if (preg_match('/[^\x00-\x7F]/u', $text)) return true;

        // Layer 2: exclusive stopwords from the most common non-English languages
        $stopwords = [
            // Spanish — formal/business
            'del', 'las', 'los', 'para', 'pero', 'tambien', 'desde', 'hasta', 'hacia',
            'nosotros', 'ustedes', 'ellos', 'ellas', 'vosotros',
            'empresa', 'empresas', 'servicios', 'productos', 'nuestro', 'nuestra',
            'nuestros', 'nuestras', 'somos', 'tenemos', 'dedicamos', 'dedicada',
            'ofrecemos', 'brindamos', 'especializada', 'especializado',
            // Spanish — conversational (catches everyday phrases like "hola como estas")
            'hola', 'como', 'estas', 'este', 'esta', 'estos', 'esas',
            'que', 'porque', 'cuando', 'donde', 'quien', 'quienes',
            'muy', 'bien', 'todo', 'todos', 'toda', 'todas',
            // French
            'dans', 'avec', 'cette', 'sont', 'ils', 'elles', 'leurs', 'mais', 'aussi',
            'nous', 'vous', 'leur', 'notre', 'nos', 'pour', 'par', 'sur',
            // German
            'und', 'ist', 'nicht', 'sich', 'auch', 'wird', 'durch', 'oder', 'aber', 'eine',
            'beim', 'haben', 'werden', 'mit', 'den', 'dem', 'des', 'ein',
            // Portuguese
            'pelo', 'pela', 'isso', 'essa', 'eles', 'elas', 'nosso', 'nossa',
            'para', 'como', 'que', 'nos', 'seu', 'sua',
            // Italian
            'questo', 'hanno', 'dello', 'degli', 'agli', 'nelle', 'negli', 'sulla',
            'sono', 'siamo', 'della', 'delle', 'negli',
            // Dutch
            'voor', 'zijn', 'naar', 'het', 'wordt', 'kunnen', 'van', 'met',
        ];

        $words   = preg_split('/[\s\.,;:]+/', mb_strtolower(trim($text)), -1, PREG_SPLIT_NO_EMPTY);
        $matches = count(array_intersect($words, $stopwords));

        return $matches >= 2;
    }

    public static function languageHint(?string $text): ?string
    {
        if (blank($text) || mb_strlen(trim($text)) < 4) return null;
        return static::isNonEnglish($text)
            ? '⚠ Text appears to be in a language other than English'
            : null;
    }
}
