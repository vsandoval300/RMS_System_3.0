<?php

namespace App\Filament\Resources\Treaties;

//use App\Filament\Resources\Treaties\TreatyResource;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Components\Grid;
use Filament\Actions\ActionGroup;
use Filament\Actions\ViewAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Resources\Treaties\RelationManagers\DocsRelationManager;
use App\Filament\Resources\Treaties\Pages\ListTreaties;
use App\Filament\Resources\Treaties\Pages\CreateTreaty;
use App\Filament\Resources\Treaties\Pages\EditTreaty;
use App\Filament\Resources\Treaties\Pages\ViewTreaty;
use App\Filament\Resources\TreatyResource\Pages;
use App\Filament\Resources\TreatyResource\RelationManagers;
use App\Models\Treaty;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use App\Models\Reinsurer;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Illuminate\Support\Facades\Storage;           // 👈 importa la facade
use Filament\Forms\Get;
use Filament\Support\Enums\VerticalAlignment;
use Filament\Actions\Action;
use Illuminate\Support\HtmlString;
use Filament\Infolists\Components\ViewEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Group;

class TreatyResource extends Resource
{
    protected static ?string $model = Treaty::class;

    protected static string | \UnitEnum | null $navigationGroup = 'Underwritten';
    protected static ?int    $navigationSort  = 17;   // aparecerá primero
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-minus';

     /* ───── NUEVO: burbuja con el total en el menú ───── */
    public static function getNavigationBadge(): ?string
    {
        return Treaty::count();
    } 

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([

                Section::make('General Details')
                    ->columnSpanFull()
                    ->columns(3)    // ← aquí defines dos columnas
                    
                    ->schema([
                        
                        Section::make()
                            ->columns(1) // subdivide la columna 3 en 2
                            ->schema([

                                Select::make('reinsurer_id')
                                    ->label('Reinsurer')
                                    //->hiddenLabel()
                                    ->relationship('reinsurer', 'name')
                                    ->required()
                                    ->searchable()
                                    ->preload() // 👈 fuerza la carga inmediata de los options
                                    ->native(false)
                                    ->placeholder('Select a reinsurer')
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function (string $operation, $state, Set $set) {
                                        if ($operation !== 'create' || !$state) {
                                            return;
                                        }

                                        $reinsurer = Reinsurer::find($state);

                                            if (! $reinsurer) {
                                                return;
                                            }

                                            $year = Carbon::now()->format('Y');
                                            $acronym = Str::upper($reinsurer->acronym);
                                            $number = str_pad($reinsurer->cns_reinsurer ?? $reinsurer->id, 3, '0', STR_PAD_LEFT);

                                            $prefix = "TTY-{$year}-{$acronym}{$number}";

                                            // Buscar el último código existente que empiece con ese prefijo
                                            $lastTreaty = Treaty::query()
                                                ->withTrashed() // 👈 incluye borrados (deleted_at no null)
                                                ->where('treaty_code', 'like', "$prefix-%")
                                                ->orderByDesc('treaty_code')
                                                ->first();

                                                // Extraer el consecutivo y sumarle 1
                                                $lastNumber = 0;

                                                if ($lastTreaty && preg_match('/-(\d{3})$/', $lastTreaty->treaty_code, $matches)) {
                                                    $lastNumber = (int)$matches[1];
                                                }

                                                $consecutive = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);

                                                $treatyCode = "{$prefix}-{$consecutive}";

                                                $set('treaty_code', $treatyCode);
                                                
                                            }),
                                           
                                            //->columnSpan(2),
                            ])
                            ->columnSpan(2),
                    
                    
                    
                    Section::make()
                        ->columns(2) // subdivide la columna 3 en 2
                        ->schema([
                        /* TextInput::make('index')
                            ->label('Index')
                            //->inlineLabel()
                            //->hiddenLabel()
                            ->required()
                            ->numeric()
                            ->default(fn () => \App\Models\Treaty::max('index') + 1 ?? 1)
                            ->disabledOn(['create', 'edit'])
                            ->dehydrated(),  */                                

                        TextInput::make('treaty_code')
                            ->label('Treaty Code')
                            //->hiddenLabel()
                            ->placeholder('Treaty code')
                            ->disabled()
                            ->dehydrated()
                            ->required()
                            ->unique(ignoreRecord: true),  

                    ])
                    ->columnSpan(1), 

                    Section::make()
                        ->columns(3) // subdivide la columna 3 en 2
                        ->columnSpanFull()
                        ->schema([
                            Select::make('contract_type')
                                ->label('Contract Type')
                                ->options([
                                    'Treaty'   => 'Treaty',
                                    'Binder'  => 'Binder',
                                    
                                ])
                                ->required()
                                ->default('Treaty')
                                ->native(false)   // UI bonita (TomSelect)
                                ->searchable()    // opcional
                                ->preload()       // opcional: carga todas las opciones
                                //->disabledOn(['create']) // mismo comportamiento que tenías
                                ->dehydrated()
                                ->columnSpan(1),

                            TextInput::make('name')
                                ->label('Tittle')
                                //->hiddenLabel()
                                //->inlineLabel()
                                //->disabledOn(['create'])
                                ->maxLength(510)
                                ->placeholder('Fill in the treaty name')
                                ->required()
                                ->columnSpan(2),
                                //->default('DFT'),
                    ]),
                    
                        
                Section::make()
                    ->columns(3) // subdivide la columna 3 en 2l
                    ->columnSpanFull()
                    ->schema([
                        Textarea::make('description')
                            ->label('Description')
                            //->hiddenLabel()
                            ->placeholder('Fill in the treaty description')
                            ->required()
                            ->columnSpanFull()
                            ->rows(3), 
                    ]),


                //Tercera burbuja: solo el archivo
                                    /* Section::make('File Upload')
                                        ->schema([

                                            FileUpload::make('document_path')
                                                ->label('File')
                                                ->disk('s3')
                                                ->directory('reinsurers/Treaties')
                                                ->visibility('public')
                                                ->acceptedFileTypes(['application/pdf'])
                                                ->preserveFilenames(false)

                                                // 1) Subida con nombre estable (basado en id) y limpieza del anterior si cambia
                                                ->saveUploadedFileUsing(function (TemporaryUploadedFile $file, $record, Get $get) {
                                                    $treatyCode = (string) ($get('treaty_code') ?: $record?->treaty_code);
                                                    //$treatyCode = strtoupper($record?->treaty_code);

                                                    $extension  = $file->getClientOriginalExtension() ?: 'pdf';
                                                    $name       = $treatyCode . '.' . $extension;
                                                    $dir = 'reinsurers/Treaties';
                                                    Storage::disk('s3')->putFileAs($dir, $file, $name, ['visibility' => 'public']);
                                                    return "{$dir}/{$name}";
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


                                        ])
                                        ->compact(), */

                        ])
                       
                ]);       
                        
                    
    }










    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            /* ─────────────────────────  PROFILE  ───────────────────────── */
            Section::make('Treaty Profile')
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

                                // Underwritten by
                                Grid::make(12)
                                    ->extraAttributes(['style' => 'border-bottom:1px solid rgba(255,255,255,0.12); padding:2px 0;'])
                                    ->schema([
                                        TextEntry::make('gd_reinsurer_label')->hiddenLabel()->state('Underwritten by')
                                            ->weight('bold')->alignment('right')->columnSpan(3),   
                                        TextEntry::make('gd_reinsurer_value')->hiddenLabel()
                                            ->state(fn ($record) => $record->reinsurer?->name ?? '—')
                                            ->columnSpan(9),
                                    ]),

                                // Treaty Code
                                Grid::make(12)
                                    ->extraAttributes(['style' => 'border-bottom:1px solid rgba(255,255,255,0.12); padding:2px 0;'])
                                    ->schema([
                                        TextEntry::make('gd_code_label')->hiddenLabel()->state('  Treaty code')
                                            ->weight('bold')->alignment('right')->columnSpan(3),
                                        TextEntry::make('gd_code_value')->hiddenLabel()
                                            ->state(fn ($record) => $record->treaty_code ?: '—')
                                            ->columnSpan(9),
                                    ]),

                                // Index
                                /* InfoGrid::make(12)
                                    ->extraAttributes(['style' => 'border-bottom:1px solid rgba(255,255,255,0.12); padding:2px 0;'])
                                    ->schema([
                                        TextEntry::make('gd_code_label')->label('')->state('  Index')
                                            ->weight('bold')->alignment('right')->columnSpan(3),
                                        TextEntry::make('gd_code_value')->label('')
                                            ->state(fn ($record) => $record->index ?: '—')
                                            ->columnSpan(9),
                                    ]), */

                                // Name
                                Grid::make(12)
                                    ->extraAttributes(['style' => 'border-bottom:1px solid rgba(255,255,255,0.12); padding:2px 0;'])
                                    ->schema([
                                        TextEntry::make('gd_code_label')->hiddenLabel()->state('Name')
                                            ->weight('bold')->alignment('right')->columnSpan(3),
                                        TextEntry::make('gd_code_value')->hiddenLabel()
                                            ->state(fn ($record) => $record->name ?: '—')
                                            ->columnSpan(9),
                                    ]),

                                // Contract type
                                Grid::make(12)
                                    ->extraAttributes(['style' => 'border-bottom:1px solid rgba(255,255,255,0.12); padding:2px 0;'])
                                    ->schema([
                                        TextEntry::make('gd_code_label')->hiddenLabel()->state('Contract type')
                                            ->weight('bold')->alignment('right')->columnSpan(3),
                                        TextEntry::make('gd_code_value')->hiddenLabel()
                                            ->state(fn ($record) => $record->contract_type ?: '—')
                                            ->columnSpan(9),
                                    ]),    

                                // Description (multilínea)
                                Grid::make(12)
                                    ->extraAttributes(['style' => 'border-bottom:1px solid rgba(255,255,255,0.12); padding:2px 0;'])
                                    ->schema([
                                        TextEntry::make('act_label')->hiddenLabel()->state('Description')
                                            ->weight('bold')->alignment('right')->columnSpan(3),
                                        TextEntry::make('act_value')->hiddenLabel()
                                            ->state(fn ($record) => $record->description ?: '—')
                                            ->extraAttributes(['style' => 'line-height:1.35;'])
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
            ->columns([
                //
                TextColumn::make('row_number')
                    ->label('#')
                    ->alignCenter()
                    ->state(function (Treaty $record) {
                        return Treaty::query()
                            ->where(function ($q) use ($record) {
                                $q->where('created_at', '<', $record->created_at)
                                ->orWhere(function ($q) use ($record) {
                                    $q->where('created_at', '=', $record->created_at)
                                        ->where('treaty_code', '<', $record->treaty_code); // 👈 desempate (ASC)
                                });
                            })
                            ->count() + 1;
                    })
                    ->alignCenter(),

                TextColumn::make('treaty_code')
                    //->verticalAlignment(VerticalAlignment::Start)
                    ->sortable(),

                /* TextColumn::make('index')
                    ->verticalAlignment(VerticalAlignment::Start)
                    ->sortable(),     */

                TextColumn::make('reinsurer.short_name')
                    //->verticalAlignment(VerticalAlignment::Start)
                    ->label('Reinsurer')
                    ->searchable(),    

                TextColumn::make('name')
                    //->verticalAlignment(VerticalAlignment::Start)
                    ->label('Tittle')
                    ->sortable()
                    ->searchable()
                    ->wrap() // ✅ Permite que se haga multilínea
                    ->extraAttributes([
                        'class' => 'max-w-xl whitespace-normal', // ✅ Deja que el texto se envuelva
                    ]),

                TextColumn::make('contract_type')
                    //->verticalAlignment(VerticalAlignment::Start)
                    ->searchable(),

                TextColumn::make('description')
                    //->verticalAlignment(VerticalAlignment::Start)
                    ->label('Description')
                    ->sortable()
                    ->searchable()
                    ->wrap() // ✅ Permite que se haga multilínea
                    ->extraAttributes([
                        'class' => 'max-w-xl whitespace-normal', // ✅ Deja que el texto se envuelva
                    ]),

                // 👉 Nombre del archivo (solo texto)
                /* TextColumn::make('document_path')
                    ->verticalAlignment(VerticalAlignment::Start)
                    ->label('File')
                    // Muestra solo el nombre del archivo
                    ->formatStateUsing(fn ($state) => $state ? basename($state) : '—')
                    ->icon(fn ($state, $record) =>
                        $record->document_path ? 'heroicon-o-document-text' : 'heroicon-o-x-circle'
                    )
                    ->color(fn ($state, $record) =>
                        $record->document_path ? 'primary' : 'danger'
                    )
                    ->tooltip(fn ($state, $record) =>
                        $record->document_path ? 'View PDF' : 'No document available'
                    )
                    ->extraAttributes([
                        'class' => 'cursor-pointer', // que parezca clickeable
                    ])
                    ->searchable()
                    ->sortable()
                    ->action(
                        Action::make('viewPdf')
                            ->label('View PDF')
                            ->hidden(fn ($record) => blank($record->document_path))
                            ->modalHeading(fn ($record) => "PDF – {$record->id}")
                            ->modalWidth('7xl')
                            ->modalSubmitAction(false)
                            ->modalContent(function ($record) {
                                $path = $record->document_path;

                                if (blank($path)) {
                                    return new HtmlString('<p>No document available.</p>');
                                }

                                // @var \Illuminate\Filesystem\FilesystemAdapter $disk
                                $disk = Storage::disk('s3');

                                // Si viene una URL completa, intentamos recuperar solo la "key" del objeto
                                if (filter_var($path, FILTER_VALIDATE_URL)) {
                                    // Ejemplo simple: quitar dominio de S3 y quedarnos con la key
                                    $parsed = parse_url($path);
                                    $key = ltrim($parsed['path'] ?? '', '/');
                                } else {
                                    $key = $path;
                                }

                                if (! $disk->exists($key)) {
                                    return new HtmlString(
                                        '<p>The PDF file does not exist in S3.</p>'
                                        .'<p><code>' . e($key) . '</code></p>'
                                    );
                                }

                                // 🔥 Siempre generamos una URL temporal con headers "inline"
                                $url = $disk->temporaryUrl(
                                    $key,
                                    now()->addMinutes(10),
                                    [
                                        'ResponseContentType'        => 'application/pdf',
                                        'ResponseContentDisposition' => 'inline; filename="'.basename($key).'"',
                                    ]
                                );

                                return view('filament.components.pdf-viewer', [
                                    'url' => $url,
                                ]);
                            })
                    ), */
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ActionGroup::make([

                    ViewAction::make()
                        ->label('View')
                        ->url(fn (Treaty $record) =>
                            TreatyResource::getUrl('view', ['record' => $record])
                ),
                       
                    //Tables\Actions\ViewAction::make(),
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
            DocsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTreaties::route('/'),
            'create' => CreateTreaty::route('/create'),
            'edit' => EditTreaty::route('/{record}/edit'),
            'view' => ViewTreaty::route('/{record}/view'), // 👈 Asegúrate que esto esté
        ];
    }
}
