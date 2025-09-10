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



class ClientsResource extends Resource
{
    protected static ?string $model = Client::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
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
                        // ───── Regla: alfanumérico + al menos una letra ─────
                        ->rules(['regex:/^(?=.*[A-Za-z])[A-Za-z0-9]+$/'])
                        ->validationMessages([
                            'regex' => 'The name must contain letters and may include numbers, '
                                    . 'but it cannot consist of numbers only.',
                        ])
                        // (opcional) formatea la capitalización
                        ->afterStateUpdated(fn ($state, callable $set) =>
                            $set('name', ucwords(strtolower($state)))
                        ),
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
        ->maxWidth('7xl')
        ->collapsible(),
    ]);
}




















    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
                TextColumn::make('id')
                    ->sortable(),
                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->extraAttributes([
                        'style' => 'width: 200px; white-space: normal;', // ✅ Deja que el texto se envuelva
                    ]),

                TextColumn::make('short_name')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('description')
                    ->label('Description')
                    ->wrap()
                    ->extraAttributes([
                        'style' => 'width: 520px; white-space: normal;', // ancho fijo de 300px
                    ])
                    ->toggleable(),
                    
                TextColumn::make('webpage')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                    
                TextColumn::make('country.name')
                    ->label('Country')
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
