<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BusinessResource\Pages;
use App\Filament\Resources\BusinessResource\RelationManagers;
use App\Models\Business;
use App\Models\Reinsurer;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Illuminate\Support\Str;
use App\Filament\Resources\BusinessResource\Widgets;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\DatePicker;


class BusinessResource extends Resource
{
    protected static ?string $model = Business::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Underwritten';
    protected static ?int    $navigationSort  = 9;   // aparecerá primero

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
            ])
            ->withCount([
                'operativeDocs',
            ]);
    }



    public static function form(Form $form): Form
    {
        return $form
            ->schema([


                Section::make('General Details')
                ->columns(2)    // ← aquí defines dos columnas
                
                ->schema([

                    // 🟢 Panel izquierdo
                    Section::make()
                        ->schema([
                        // ╔═════════════════════════════════════════════════════════════════════════╗
                        // ║ Reinsurer field selector linked to Business Code                        ║
                        // ╚═════════════════════════════════════════════════════════════════════════╝
                            Select::make('reinsurer_id')
                                ->label('Reinsurer')
                                ->relationship('reinsurer', 'name')
                                ->searchable()
                                ->preload() // 👈 fuerza la carga inmediata de los options
                                 ->native(false)
                                ->live(onBlur: true)
                                ->afterStateUpdated(function (string $operation, $state, Forms\Set $set) {
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

                                    $prefix = "{$year}-{$acronym}{$number}";

                                    // Buscar el último código existente que empiece con ese prefijo
                                    $lastBusiness = Business::query()
                                        ->where('business_code', 'like', "$prefix-%")
                                        ->orderByDesc('business_code')
                                        ->first();

                                    // Extraer el consecutivo y sumarle 1
                                    $lastNumber = 0;

                                    if ($lastBusiness && preg_match('/-(\d{3})$/', $lastBusiness->business_code, $matches)) {
                                        $lastNumber = (int)$matches[1];
                                    }

                                    $consecutive = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);

                                    $businessCode = "{$prefix}-{$consecutive}";

                                    $set('business_code', $businessCode);
                                }),
                                
                               

                            Textarea::make('description')
                                ->label('Description')
                                ->required()
                                ->columnSpanFull()
                                ->rows(5), // 👈 aumenta el número de líneas visibles

                            // 👇 Este es el espaciador que empareja visualmente la altura
                           /*  Placeholder::make('spacer')
                                ->content('')
                                ->hiddenLabel()
                                ->extraAttributes(['style' => 'height: 3rem']), */
                        ])
                        ->columnSpan(1),

                    // 🔵 Panel derecho (dos burbujas una debajo de otra)
                    Section::make()
                        ->schema([
                            // Primera burbuja: Index + Business Code
                            Section::make()
                                ->columns(2)
                                ->schema([
                                    TextInput::make('index')
                                        ->label('Index')
                                        ->required()
                                        ->numeric()
                                        ->default(fn () => \App\Models\Business::max('index') + 1 ?? 1)
                                        ->disabledOn(['create', 'edit'])
                                        ->dehydrated(), // 👈 esto asegura que se envíe el valor
                                 // ╔═════════════════════════════════════════════════════════════════════════╗
                                 // ║ Business Code que se va a crear dependiendo el reasegurador que exista  ║
                                 // ╚═════════════════════════════════════════════════════════════════════════╝
                                    TextInput::make('business_code')
                                        ->label('Business Code')
                                        ->disabled()
                                        ->dehydrated()
                                        ->required()
                                        ->unique(),
                                ]),

                            // Segunda burbuja: Lifecycle status (una sola columna)
                            Section::make()
                                ->schema([
                                    TextInput::make('business_lifecycle_status')
                                        ->label('Business Lifecycle Status')
                                        ->required()
                                        ->maxLength(510)
                                        ->default('On Hold')
                                        ->disabledOn(['create', 'edit'])
                                        ->dehydrated(), // 👈 esto asegura que se envíe el valor
                                ]),
                        ])
                        ->columnSpan(1),
                    
                
                ]),

                   Section::make('Contract Details')
                    ->columns(3)
                    
                        ->schema([
                    
                        
                            Select::make('reinsurance_type')
                            ->label('Reinsurer Type')
                            ->placeholder('Select a reinsurer type') // 👈 Aquí cambias el texto
                            ->options([
                                'Facultative' => 'Facultative',
                                'Treaty' => 'Treaty',
                            ])
                            ->required()
                            ->searchable(),        

                            Select::make('risk_covered')
                            ->label('Risk Covered')
                            ->placeholder('Select the risk covered.') // 👈 Aquí cambias el texto
                            ->options([
                                'Life' => 'Life',
                                'Non-Life' => 'Non-Life',
                            ])
                            ->required()
                            ->searchable(),
                            
                            Select::make('business_type')
                            ->label('Business Type')
                            ->placeholder('Select a business type.') // 👈 Aquí cambias el texto
                            ->options([
                                'Own' => 'Own',
                                'Third Party' => 'Third party',
                            ])
                            ->required()
                            ->searchable(),

                            Select::make('premium_type')
                            ->label('Premium Type')
                            ->placeholder('Select a premium type.') // 👈 Aquí cambias el texto
                            ->options([
                                'Fixed' => 'Fixed',
                                'Estimated' => 'Estimated',
                            ])
                            ->required()
                            ->searchable(),

                            Select::make('purpose')
                            ->label('Purpose')
                            ->placeholder('Select business purpose.') // 👈 Aquí cambias el texto
                            ->options([
                                'Normal' => 'Normal',
                                'Strategic' => 'Strategic',
                            ])
                            ->required()
                            ->searchable(),

                            Select::make('claims_type')
                            ->label('Claims Type')
                            ->placeholder('Select claims type.') // 👈 Aquí cambias el texto
                            ->options([
                                'Claims Ocurrence' => 'Claims occurrence',
                                'Claims Made' => 'Claims made',
                            ])
                            ->required()
                            ->searchable(),

                            Select::make('producer_id')
                                ->label('Producer')
                                ->relationship('Producer', 'name') // usa la relación en tu modelo
                                ->searchable()
                                ->preload()
                                ->required(),

                            Select::make('currency_id')
                                ->label('Currency')
                                ->relationship(
                                    name: 'currency',         // ← relación en tu modelo
                                    titleAttribute: 'name')
                                ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->acronym} - {$record->name}")
                                ->searchable()
                                ->preload()
                                ->required(),

                            Select::make('region_id')
                                ->label('Region')
                                ->relationship('Region', 'name') // usa la relación en tu modelo
                                ->searchable()
                                ->preload()
                                ->required(),
                    
                 ]),   // ← cierra schema() y luego la Sección

                  Section::make()
                    ->schema([
                        Grid::make(2)
                            ->schema([

                                Section::make('Relationship Info')
                                    ->schema([
                                        Select::make('parent_id')
                                            ->label('Parent Business')
                                            ->relationship('parent', 'business_code')
                                            ->searchable()
                                            ->preload()
                                            ->nullable(),

                                        Select::make('renewed_from_id')
                                            ->label('Renewed From')
                                           ->relationship('renewedFrom', 'business_code')
                                            ->searchable()
                                            ->preload()
                                            ->nullable(),
                                    ])
                                    ->columnSpan(1), // 👈 fuerza que la sección ocupe solo la mitad

                                Section::make('Status Tracking')
                                    ->schema([
                                        Forms\Components\TextInput::make('approval_status')
                                            ->required()
                                            ->maxLength(510)
                                            ->default('DFT'),

                                        Forms\Components\DateTimePicker::make('approval_status_updated_at'),
                                    ])
                                    ->columnSpan(1), // 👈 también aquí
                            ]),
                    ]),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('index')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('business_code')
                    ->searchable(),
                Tables\Columns\TextColumn::make('reinsurance_type')
                    ->searchable(),
                Tables\Columns\TextColumn::make('reinsurer.short_name')
                    ->label('Reinsurer')
                    ->searchable(),
                Tables\Columns\TextColumn::make('renewed_from_id')
                    ->searchable(),
                Tables\Columns\TextColumn::make('currency.acronym')
                    ->label('Currency')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('business_lifecycle_status')
                    ->label('Lifecycle')
                    ->badge()
                    ->color(fn ($state) => match ($state->value) {
                        'On Hold'   => 'gray',
                        'Pending'   => 'warning',
                        'In Force'  => 'success',
                        'To Expire' => 'info',
                        'Expired'   => 'danger',
                        'Cancelled' => 'gray',
                        default     => 'secondary',
                    })
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('operative_docs_count')
                    ->counts('operativeDocs')
                    ->label('Documents')
                    ->sortable()
                    ->formatStateUsing(fn ($state) => "$state document" . ($state === 1 ? '' : 's')) // 👈 esto agrega el texto
                    ->badge()
                    ->color(fn (int $state): string => $state > 0 ? 'primary' : 'gray'),



            ])
            
            ->filters([
                //
            ])
            
            // ╔═════════════════════════════════════════════════════════════════════════╗
            // ║ Underwritten Report                                                     ║
            // ╚═════════════════════════════════════════════════════════════════════════╝

            ->headerActions([
                Action::make('export')
                    ->label('Export Report')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->form([
                        Select::make('report_type')
                            ->label('Report Type')
                            ->options([
                                'summary' => 'Summary',
                                'detailed' => 'Detailed',
                            ])
                            ->required(),

                        Select::make('format')
                            ->label('Export Format')
                            ->options([
                                'pdf' => 'PDF',
                                'xlsx' => 'Excel'
                            ])
                            ->default('pdf')
                            ->required(),

                        DatePicker::make('from_date')->label('From Date'),
                        DatePicker::make('to_date')->label('To Date'),
                    ])
                    ->modalHeading('Export Business Report')
                    ->modalSubmitActionLabel('Generate')
                    ->action(function (array $data) {
                        // Aquí generas y descargas el reporte según $data

                        $from = $data['from_date'] ?? null;
                        $to = $data['to_date'] ?? null;
                        $type = $data['report_type'];
                        $format = $data['format'];

                        $query = \App\Models\Business::query();

                        if ($from) $query->whereDate('created_at', '>=', $from);
                        if ($to) $query->whereDate('created_at', '<=', $to);

                        $businesses = $query->get();

                        // Aquí puedes usar Laravel Excel o DomPDF para exportar
                        // return response()->download(...);
                    }),
            ])


            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make()
                    ->label('View')
                    ->url(fn (Business $record) =>
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
            //
            RelationManagers\LiabilityStructuresRelationManager::class,
            RelationManagers\OperativeDocsRelationManager::class,
            

            
            
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBusinesses::route('/'),
            'create' => Pages\CreateBusiness::route('/create'),
            'edit' => Pages\EditBusiness::route('/{record}/edit'),
            'view' => Pages\ViewBusiness::route('/{record}/view'), // 👈 Asegúrate que esto esté
        ];
    }

    public static function getWidgets(): array
    {
        return [
            Widgets\BusinessStatsOverview::class,
        ];
    }




}
