<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ClientsResource\Pages;
use App\Filament\Resources\ClientsResource\RelationManagers;
use App\Models\Client;
use App\Models\Country;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;

use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Grid as InfoGrid;
use Filament\Infolists\Components\Section as InfoSection;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Support\Enums\VerticalAlignment;



class ClientsResource extends Resource
{
    protected static ?string $model = Client::class;
    protected static ?string $navigationIcon = 'heroicon-o-minus';
    protected static ?string $navigationGroup = 'Customers';

    /* ───── NUEVO: burbuja con el total en el menú ───── */
    public static function getNavigationBadge(): ?string
    {
        // Puedes usar self::$model::count() o Reinsurer::count()
        return Client::count();
    }

    public static function getTableQuery(): Builder
    {
        return Client::query()->with('country');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
                Section::make('Client Details')
                ->columns(2)    // ← aquí defines dos columnas
                ->schema([
                    

                    TextInput::make('name')
                        ->label(__('Name'))
                        ->required()
                        ->placeholder("Please provide client's name")
                        ->unique(ignorable: fn (?Model $record) => $record)
                        ->maxLength(255)
                        // Permite letras unicode, dígitos, espacios y puntos. Exige al menos una letra.
                        ->rules(['regex:/^(?=.*\p{L})[\p{L}\d .]+$/u'])
                        ->validationMessages([
                            'regex' => 'The name must contain letters and may include numbers, spaces, and dots.',
                        ])
                        ->afterStateUpdated(function ($state, callable $set) {
                            $value = (string) $state;

                            // 1) Limpia espacios repetidos y bordes
                            $value = preg_replace('/\s+/', ' ', trim($value));

                            // 2) Pasa a minúsculas para normalizar y separa en palabras
                            $lower = mb_strtolower($value, 'UTF-8');
                            $words = preg_split('/\s/u', $lower, -1, PREG_SPLIT_NO_EMPTY);

                            // 3) Partículas que van en minúsculas (salvo si son la primera palabra)
                            $particles = ['de','del','la','las','el','los','y','e','o','u','al'];

                            foreach ($words as $i => $w) {
                                if ($i === 0 || !in_array($w, $particles, true)) {
                                    // Title-case respetando acentos
                                    $words[$i] = mb_convert_case($w, MB_CASE_TITLE, 'UTF-8');
                                } else {
                                    $words[$i] = $w; // mantener en minúsculas
                                }
                            }

                            $result = implode(' ', $words);

                            // 4) Normaliza abreviaturas y razones sociales (orden: de más larga a más corta)
                            $patterns = [
                                // S. de R.L. de C.V.
                                '/\bS\.?\s*DE\s*R\.?\s*L\.?\s*DE\s*C\.?\s*V\.?\b/ui' => 'S. de R.L. de C.V.',
                                // S. de R.L.
                                '/\bS\.?\s*DE\s*R\.?\s*L\.?\b/ui'                   => 'S. de R.L.',
                                // S.A.P.I
                                '/\bS\.?\s*A\.?\s*P\.?\s*I\.?\b/ui'                 => 'S.A.P.I',
                                // S.A.
                                '/\bS\.?\s*A\.?\b/ui'                               => 'S.A.',
                                // C.V.
                                '/\bC\.?\s*V\.?\b/ui'                               => 'C.V.',
                            ];
                            $result = preg_replace(array_keys($patterns), array_values($patterns), $result);

                            // 5) Ajuste fino: si una partícula quedó justo después de punto (p. ej., "S.A. De"),
                            //    queremos "de" en minúsculas.
                            $result = preg_replace_callback(
                                '/([A-Z]\.)\s+(De|Del|La|Las|El|Los|Y|E|O|U)\b/u',
                                fn($m) => $m[1] . ' ' . mb_strtolower($m[2], 'UTF-8'),
                                $result
                            );

                            $set('name', $result);
                        }),
                        //->helperText('First letter of each word will be capitalised.'),
                        
                    
                    TextInput::make('short_name')
                        ->label(__('Short Name'))
                        ->required()
                        ->placeholder("Please provide client's short name")
                        ->unique(ignorable: fn (?Model $record) => $record)   // 👈 ignora el registro actual
                        ->live(onBlur: false)
                        ->maxLength(255)
                        ->afterStateUpdated(fn ($state, callable $set) =>
                             $set('short_name', ucwords(strtolower($state)))),
                        //->helperText('First letter of each word will be capitalised.'),
                       
                    
                    Textarea::make('description')
                        ->label(__('Description'))
                        ->required()
                        ->placeholder('Enter your company’s main business activity.')
                        ->columnSpan('full')
                        ->autosize()
                        ->afterStateUpdated(fn ($state, callable $set) => $set('description', ucfirst(strtolower($state)))),
                        //->helperText('Please provide a brief description of the sector.'),
                        

                    TextInput::make('webpage')
                        ->label(__('Web Page'))
                        ->required()
                        ->placeholder('https://www.example.com')
                        ->maxLength(255)
                        ->rule('url'),
                        //->helperText('First letter of each word will be capitalised.'),
                        

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
                        ->placeholder('Choose the reinsurer\'s country')
                        ->required()
                        ->placeholder('Select a country'),
                        //->helperText('Choose the reinsurer\'s country.'),
                        
                    Select::make('industries')             // ① nombre del campo (puede ser cualquiera)
                        ->label('Industries')              // ② texto mostrado
                        ->relationship('industries', 'name') // ③ usa la rel. + columna a mostrar
                        ->multiple()                       // ④ habilita selección múltiple
                        ->preload() 
                        ->placeholder('Choose the reinsurer\'s industries')                       // ⑤ carga todas las opciones de golpe
                        ->searchable()                     // ⑥ añade buscador
                        ->columnSpan('full')               // ⑦ opcional: que ocupe todo el ancho
                        ->visible(fn (string $context): bool => $context === 'create'),

                ]),

                Section::make('Images')->schema([

                    FileUpload::make('logo_path')
                        ->label(__('Logo'))
                        ->disk('s3')
                        ->directory('reinsurers/logos')
                        ->image()
                        ->visibility('public')
                        ->default(fn ($record) => $record?->logo)
                        ->imagePreviewHeight('100')
                        ->previewable()
                        ->extraAttributes(['class' => 'w-1/2']),

                    

                ]),    

            ]);
    }



public static function infolist(Infolist $infolist): Infolist
{
    return $infolist->schema([
        InfoSection::make('Client Profile')->schema([
            InfoGrid::make(3)
                ->extraAttributes(['style' => 'gap: 6px;'])
                ->schema([

                    /* ── Cols 1–2: filas “Label + Value” ── */
                    InfoGrid::make(1)
                        ->columnSpan(2)
                        ->extraAttributes(['style' => 'row-gap: 0;'])
                        ->schema([

                            // Name
                            InfoGrid::make(12)
                                ->extraAttributes(['style' => 'border-bottom:1px solid rgba(255,255,255,0.12); padding:2px 0;'])
                                ->schema([
                                    TextEntry::make('name_label')
                                        ->label('')
                                        ->state('Name:')
                                        ->weight('bold')
                                        ->alignment('right')
                                        ->columnSpan(3),
                                    TextEntry::make('name_value')
                                        ->label('')
                                        ->state(fn ($record) => $record->name ?: '—')
                                        ->columnSpan(9),
                                ]),

                            // Short Name
                            InfoGrid::make(12)
                                ->extraAttributes(['style' => 'border-bottom:1px solid rgba(255,255,255,0.12); padding:2px 0;'])
                                ->schema([
                                    TextEntry::make('short_label')
                                        ->label('')
                                        ->state('Short Name:')
                                        ->weight('bold')
                                        ->alignment('right')
                                        ->columnSpan(3),
                                    TextEntry::make('short_value')
                                        ->label('')
                                        ->state(fn ($record) => $record->short_name ?: '—')
                                        ->columnSpan(9),
                                ]),

                            // Description
                            InfoGrid::make(12)
                                ->extraAttributes(['style' => 'border-bottom:1px solid rgba(255,255,255,0.12); padding:2px 0;'])
                                ->schema([
                                    TextEntry::make('desc_label')
                                        ->label('')
                                        ->state('Description:')
                                        ->weight('bold')
                                        ->alignment('right')
                                        ->columnSpan(3),
                                    TextEntry::make('desc_value')
                                        ->label('')
                                        ->state(fn ($record) => $record->description ?: '—')
                                        ->extraAttributes(['style' => 'line-height:1.35;'])
                                        ->columnSpan(9),
                                ]),

                            // Web Page (link)
                            InfoGrid::make(12)
                                ->extraAttributes(['style' => 'border-bottom:1px solid rgba(255,255,255,0.12); padding:2px 0;'])
                                ->schema([
                                    TextEntry::make('web_label')
                                        ->label('')
                                        ->state('Web Page:')
                                        ->weight('bold')
                                        ->alignment('right')
                                        ->columnSpan(3),
                                    TextEntry::make('web_value')
                                        ->label('')
                                        ->state(fn ($record) => $record->webpage ?: '—')
                                        ->url(fn ($record) => $record->webpage
                                            ? (str_starts_with($record->webpage, 'http://') || str_starts_with($record->webpage, 'https://')
                                                ? $record->webpage
                                                : 'https://' . $record->webpage)
                                            : null
                                        )
                                        ->openUrlInNewTab()
                                        ->columnSpan(9),
                                ]),

                            // Country
                            InfoGrid::make(12)
                                ->extraAttributes(['style' => 'border-bottom:1px solid rgba(255,255,255,0.12); padding:2px 0;'])
                                ->schema([
                                    TextEntry::make('country_label')
                                        ->label('')
                                        ->state('Country:')
                                        ->weight('bold')
                                        ->alignment('right')
                                        ->columnSpan(3),
                                    TextEntry::make('country_value')
                                        ->label('')
                                        ->state(fn ($record) => $record->country
                                            ? "{$record->country->alpha_3} - {$record->country->name}"
                                            : '—'
                                        )
                                        ->columnSpan(9),
                                ]),

                            // Industries (chips)
                            InfoGrid::make(12)
                                ->extraAttributes(['style' => 'border-bottom:1px solid rgba(255,255,255,0.12); padding:2px 0;'])
                                ->schema([
                                    TextEntry::make('industries_label')
                                        ->label('')
                                        ->state('Industries:')
                                        ->weight('bold')
                                        ->alignment('right')
                                        ->columnSpan(3),
                                    TextEntry::make('industries_value')
                                        ->label('')
                                        ->html()
                                        ->state(function ($record) {
                                            $names = $record->industries?->pluck('name')->all() ?? [];
                                            if (empty($names)) return '—';
                                            return collect($names)->map(fn ($n) =>
                                                "<span style='display:inline-block;padding:2px 8px;border-radius:9999px;background:rgba(255,255,255,0.08);font-size:12px;margin-right:6px;'>{$n}</span>"
                                            )->implode('');
                                        })
                                        ->columnSpan(9),
                                ]),
                        ]),


                    /* ── Col 3: burbuja del logo ── */
                    InfoGrid::make(1)
                        ->columnSpan(1)
                        ->extraAttributes(['style' => 'display:flex;flex-direction:column;gap:6px;height:100%;'])
                        ->schema([
                            TextEntry::make('logo_title')
                                ->label('')->state('Logo')->weight('bold')
                                ->extraAttributes(['style' => 'margin:0 0 4px 2px;']),

                            ImageEntry::make('logo_img')
                                ->label('')
                                ->disk('s3')
                                ->visibility('public')
                                ->state(fn ($record) => $record->logo_path ?? $record->logo ?? null)
                                ->hidden(fn ($record) => blank($record->logo_path ?? $record->logo))
                                ->extraAttributes([
                                    'style' => '
                                        min-height:260px; width:100%;
                                        border-radius:14px;
                                        background:linear-gradient(135deg, rgba(255,255,255,0.08), rgba(255,255,255,0.03));
                                        border:1px solid rgba(255,255,255,0.15);
                                        display:flex; align-items:center; justify-content:center;
                                        padding:8px; margin:0; overflow:hidden;
                                    ',
                                ])
                                ->extraImgAttributes([
                                    'style' => 'width:96%;height:96%;object-fit:contain;display:block;',
                                ]),

                            TextEntry::make('logo_placeholder')
                                ->label('')->html()
                                ->state('
                                    <div style="
                                        min-height:260px; width:100%;
                                        border-radius:14px;
                                        display:flex; align-items:center; justify-content:center;
                                        margin:0;
                                        border:1px dashed rgba(255,255,255,0.25);
                                        background:linear-gradient(135deg, rgba(255,255,255,0.04), rgba(255,255,255,0.02));
                                    "></div>
                                ')
                                ->visible(fn ($record) => blank($record->logo_path ?? $record->logo))
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
                TextColumn::make('id')
                    ->verticalAlignment(VerticalAlignment::Start)
                    ->sortable(),

                TextColumn::make('name')
                    ->verticalAlignment(VerticalAlignment::Start)
                    ->searchable()
                    ->sortable()
                    ->extraAttributes([
                        'style' => 'width: 200px; white-space: normal;', // ✅ Deja que el texto se envuelva
                    ]),

                TextColumn::make('short_name')
                    ->verticalAlignment(VerticalAlignment::Start)
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('description')
                    ->label('Description')
                    ->verticalAlignment(VerticalAlignment::Start)
                    ->wrap()
                    ->extraAttributes([
                        'style' => 'width: 520px; white-space: normal;', // ancho fijo de 300px
                    ])
                    ->toggleable(),
                    
                TextColumn::make('webpage')
                        ->verticalAlignment(VerticalAlignment::Start)
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                    
                TextColumn::make('country.name')
                    ->label('Country')
                    ->verticalAlignment(VerticalAlignment::Start)
                    ->sortable()
                    ->searchable()
                    ->toggleable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([

                    Tables\Actions\ViewAction::make()
                        ->label('View')
                        ->url(fn (Client $record) =>
                            ClientsResource::getUrl('view', ['record' => $record])
                ),
                       
                    //Tables\Actions\ViewAction::make(),
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
            
           RelationManagers\IndustriesRelationManager::class,
        
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListClients::route('/'),
            'create' => Pages\CreateClients::route('/create'),
            'view'   => Pages\ViewClients::route('/{record}'),  // 👈 nuevo
            'edit'   => Pages\EditClients::route('/{record}/edit'),
        ];
    }
}
