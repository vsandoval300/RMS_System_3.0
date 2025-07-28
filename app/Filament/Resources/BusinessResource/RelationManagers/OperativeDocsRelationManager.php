<?php

namespace App\Filament\Resources\BusinessResource\RelationManagers;

use App\Models\BusinessDocType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;           // 👈 importa la facade
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Filament\Forms\Components\FileUpload;
use Filament\Support\Enums\VerticalAlignment;
use Icetalker\FilamentTableRepeater\Forms\Components\TableRepeater;
use Filament\Support\RawJs;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use App\Models\CostScheme;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\View;
use Filament\Forms\Components\Hidden;



use Nette\Utils\Html as UtilsHtml;

class OperativeDocsRelationManager extends RelationManager
{
    protected static string $relationship = 'OperativeDocs';
    protected static ?string $title = 'Operative Documents';
    protected static ?string $recordTitleAttribute = 'description';


    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with([
            'docType',
            'schemes.costScheme.costNodexes', // 👈 esto es lo que faltaba para que precargue bien
        ]);
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            Tabs::make('Operative Doc Form')
                ->columnSpanFull()
                ->tabs([
                    // ╔═════════════════════════════════════════════════════════════════════════╗
                    // ║ Tab for Document Details                                                ║
                    // ╚═════════════════════════════════════════════════════════════════════════╝
                    Tab::make('Document Details')
                        ->schema([

                            // 🟦 Primera burbuja: solo Id Document
                            Section::make()
                                ->schema([
                                    Grid::make(12)
                                        ->schema([
                                            Placeholder::make('')
                                                ->columnSpan(6), // deja media fila vacía

                                            TextInput::make('id')
                                                ->label('Id Document')
                                                ->disabled()
                                                ->dehydrated()
                                                ->required()
                                                ->columnSpan(6),
                                        ]),
                                ])
                                ->compact(),

                            // 🟦 Segunda burbuja: el resto de los campos
                            Section::make('Details')
                                ->schema([
                                    Textarea::make('description')
                                        ->label('Tittle')
                                        ->maxLength(255)
                                        ->columnSpanFull(),

                                    Select::make('operative_doc_type_id')
                                        ->label('Document Type')
                                        ->relationship('docType', 'name')
                                        ->required(),

                                    Toggle::make('client_payment_tracking')
                                        ->label('Client Payment Tracking')
                                        ->default(false)
                                        ->helperText('Include tracking of payments from the original client if this option is enabled.'),

                                    DatePicker::make('inception_date')
                                        ->label('Inception Date')
                                        ->required(),

                                    DatePicker::make('expiration_date')
                                        ->label('Expiration Date')
                                        ->required()
                                        ->date()
                                        ->after('inception_date')
                                        ->validationMessages([
                                            'after' => 'The expiration date must be later than the inception date.',
                                            'required' => 'You must provide an expiration date.',
                                        ])
                                        ->afterStateUpdated(function (callable $set, $state, $get) {
                                            // lógica adicional opcional
                                        }),

                                    TextInput::make('roe')
                                        ->label('roe')
                                        ->required(),
                                ])
                                ->columns(2)
                                ->compact(),

                            // 🟦 Tercera burbuja: solo el archivo
                            Section::make('File Upload')
                                ->schema([
                                    FileUpload::make('document_path')
                                        ->label('File')
                                        ->disk('s3')
                                        ->directory('reinsurers/OperativeDocuments')
                                        ->visibility('private')
                                        ->acceptedFileTypes(['application/pdf'])
                                        ->helperText('Only PDF files are allowed.'),
                                ])
                                ->compact(),

                        ]),

                    // 🟦 Cuarta burbuja: Insureds en otro Tab
                    // ╔═════════════════════════════════════════════════════════════════════════╗
                    // ║ Tab for Insured Members.                                                ║
                    // ╚═════════════════════════════════════════════════════════════════════════╝
                    Tab::make('Insured Members')
                        ->schema([
                            Grid::make(12)
                                ->schema([
                                    TableRepeater::make('insureds')
                                        ->label('Insureds')
                                        ->relationship()
                                        ->schema([
                                            Select::make('company_id')
                                                ->label('Company')
                                                ->relationship('company', 'name')
                                                ->preload()
                                                ->required()
                                                ->searchable()
                                                ->columnSpan(5),

                                            Select::make('coverage_id')
                                                ->label('Coverage')
                                                ->relationship('coverage', 'name')
                                                ->preload()
                                                ->required()
                                                ->searchable()
                                                ->columnSpan(5),

                                            TextInput::make('premium')
                                                ->numeric()
                                                ->prefix('$')
                                                ->required()
                                                ->mask(
                                                    RawJs::make(<<<'JS'
                                                        $money($input, '.', ',', 2)
                                                    JS)
                                                )
                                                ->dehydrateStateUsing(fn ($state) => str_replace(',', '', $state))
                                                ->columnSpan(2),
                                        ])
                                        ->defaultItems(1)
                                        ->columns(12)
                                        ->addActionLabel('Add Insured')
                                        ->reorderable(false)
                                        ->columnSpan(12) // 👈 fuerza a ocupar todo el ancho
                                        ->afterStateUpdated(function ($state, callable $set) {
                                            $total = collect($state)
                                                ->pluck('premium')
                                                ->filter()
                                                ->map(fn ($value) => floatval(str_replace(',', '', $value)))
                                                ->sum();

                                            $set('insureds_total', number_format($total, 2, '.', ','));
                                        }),

                                    Placeholder::make('')->columnSpan(8),

                                    TextInput::make('insureds_total')
                                        ->label('Grand Total Premium')
                                        ->prefix('$')
                                        ->disabled()
                                        ->dehydrated(false)
                                        ->columnSpan(4), // 👈 mismo span para alinear con el repeater
                                ]),
                        ]),
                    // ╔═════════════════════════════════════════════════════════════════════════╗
                    // ║ Tab for Placement Schemes                                               ║
                    // ╚═════════════════════════════════════════════════════════════════════════╝
                    Tab::make('Placement Schemes')
                        ->schema([
                            Repeater::make('schemes')
                                ->label('Placement Schemes')
                                ->relationship('schemes')
                                ->schema([
                                    Select::make('cscheme_id')
                                        ->label('Placement Scheme')
                                        ->options(
                                            \App\Models\CostScheme::all()->mapWithKeys(function ($scheme) {
                                                $shareFormatted = number_format($scheme->share * 100, 2) . '%';
                                                return [
                                                    $scheme->id => "{$scheme->id} · Index: {$scheme->index} · Share: {$shareFormatted} · Type: {$scheme->agreement_type}"
                                                ];
                                            })
                                        )

                                        ->searchable()
                                        ->preload()
                                        ->reactive()
                                        ->required(),

                                    Group::make()
                                        ->schema([
                                            View::make('partials.scheme-nodes-preview')
                                            ->viewData(fn ($get) => [
                                            'schemeId' => $get('cscheme_id'),
                                        ])
                                            ->columnSpan('full'),
                                        ]),
                                ])
                                ->columns(1)
                                ->defaultItems(0)
                                ->addActionLabel('Agregar esquema de colocación'),
                    ]),


                    // ╔═════════════════════════════════════════════════════════════════════════╗
                    // ║ Tab for Installments.                                                   ║
                    // ╚═════════════════════════════════════════════════════════════════════════╝
                    Tab::make('Installments')
                        ->schema([
                            TableRepeater::make('transactions')
                                ->label('Installments')
                                ->relationship()
                                ->schema([
                                   TextInput::make('index')
                                        ->label('Index')
                                        ->disabled()
                                        ->dehydrated()
                                        ->required()
                                        ->numeric()
                                        ->columnSpan(1),

                                                    
                                    TextInput::make('proportion')
                                        ->label('Proportion')
                                        ->prefix('%')
                                        ->numeric()
                                        ->required()
                                        ->minValue(0)
                                        ->maxValue(100)
                                        ->step(0.01)
                                        ->mask(RawJs::make('$money($input, ".", ",", 2)'))
                                        ->reactive()
                                        ->formatStateUsing(fn ($state) => $state !== null ? round($state * 100, 2) : null)
                                        ->dehydrateStateUsing(fn ($state) => floatval(str_replace(',', '', $state)) / 100)
                                        ->columnSpan(1),



                                   TextInput::make('exch_rate')
                                        ->label('Exchange Rate')
                                        ->numeric()
                                        ->required()
                                        ->step(0.00001)
                                        ->columnSpan(1),

                                    DatePicker::make('due_date')
                                        ->label('Due Date')
                                        ->required()
                                        ->columnSpan(1),

                                    // Campos ocultos: se asignan automáticamente
                                    Hidden::make('remittance_code')->default(null),
                                    Hidden::make('transaction_type_id')->default(1),
                                    Hidden::make('transaction_status_id')->default(1),
                                    Hidden::make('op_document_id')->default(fn () => $this->getOwnerRecord()?->id),
                                ])
                                ->defaultItems(0)
                                ->columns(4)
                                ->addActionLabel('New Installment')
                                ->afterStateUpdated(function (array $state, callable $set) {
                                    $newState = [];
                                    $index = 1;

                                    foreach ($state as $key => $item) {
                                        if (is_array($item)) {
                                            $item['index'] = $index;
                                            $newState[$key] = $item;
                                            $index++;
                                        }
                                    }

                                    $set('transactions', $newState);
                                })

                                // 🔐 Validación personalizada para 100%
                                ->rules([
                                    function (\Filament\Forms\Get $get) {
                                        return function (string $attribute, $value, \Closure $fail) use ($get) {
                                            $total = collect($get('transactions'))
                                                ->pluck('proportion')
                                                ->map(fn ($value) => floatval(str_replace(',', '', $value)))
                                                ->sum();

                                            if (abs($total - 1) > 0.0001) {
                                                $fail('La suma de las proporciones debe ser exactamente 100%.');
                                            }
                                        };
                                    }
                                ]),
                        ]),


                    Tab::make('Calculations')
                        ->schema([
                            View::make('filament.resources.business.summary-html')
                                ->viewData(fn ($get, $record) => [
                                    'id' => $get('id'),
                                    'createdAt' => optional($record)->created_at,
                                    'documentType' => optional($record->docType)->name,
                                    'inceptionDate' => $get('inception_date'),
                                    'expirationDate' => $get('expiration_date'),
                                    'premiumType' => optional($record->business)->premium_type ?? '-', // ← ✅ importante cambio aquí
                                ]),
                            ]),






                ]),

                

        ]);
    }

    public function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('index')
                ->sortable()->verticalAlignment(VerticalAlignment::Start) 
                ->sortable()
                ->searchable(),

            Tables\Columns\TextColumn::make('id')
                ->label('Document code')
                ->sortable()->verticalAlignment(VerticalAlignment::Start) 
                ->sortable()
                ->searchable()
                ->tooltip(fn ($state) => $state) 
                ->extraAttributes(['class' => 'w-64']), // 👈 Ajusta el ancho

            Tables\Columns\TextColumn::make('docType.name')
                ->label('Doc Type')
                ->sortable()->verticalAlignment(VerticalAlignment::Start) 
                ->sortable()
                ->searchable(),

            Tables\Columns\TextColumn::make('description')
                ->searchable()
                ->sortable()->verticalAlignment(VerticalAlignment::Start) 
                ->wrap() // 👈 permite múltiples líneas
                ->extraAttributes([
                        'style' => 'width: 250px; white-space: normal; vertical-align: top;',
                    ]),

            Tables\Columns\TextColumn::make('inception_date')
                ->sortable()->verticalAlignment(VerticalAlignment::Start)   
                ->date(),

            Tables\Columns\TextColumn::make('expiration_date')
                ->sortable()->verticalAlignment(VerticalAlignment::Start) 
                ->date(),
            
            Tables\Columns\TextColumn::make('status')
                ->label('Status')
                ->sortable()->verticalAlignment(VerticalAlignment::Start) 
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


            Tables\Columns\IconColumn::make('document_path')
                ->label('File')                         // sin encabezado
                ->sortable()->verticalAlignment(VerticalAlignment::Start) 
                ->getStateUsing(fn ($record) => true) // ← fuerza que siempre se pinte
                ->icon(fn ($record) =>
                        $record->document_path ? 'heroicon-o-document' : 'heroicon-o-x-circle'
                    )



                ->color(fn ($record) => $record->document_path ? 'primary' : 'danger')
                ->url(function ($record) {
                    if (! $record->document_path) {
                        return null; // 👈 evita error si es null
                    }

                    /** @var \Illuminate\Filesystem\FilesystemAdapter $s3 */
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
                ),

        ])
        ->filters([
            //
        ])
        ->headerActions([
            Tables\Actions\CreateAction::make()
                ->label('Create Operative Doc')
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
                    if (! isset($data['id'])) {
                        $business = $livewire->ownerRecord;

                        $lastIndex = $business->operativeDocs()
                            ->withTrashed()
                            ->get()
                            ->map(function ($doc) {
                                return intval(substr($doc->id, -2));
                            })
                            ->max();

                        $newIndex = $lastIndex ? $lastIndex + 1 : 1;
                        $data['id'] = $business->business_code . '-' . str_pad($newIndex, 2, '0', STR_PAD_LEFT);
                    }

                    return $data;
                })
                //->formModalWidth('xl') // opcional: para mayor espacio
        ])
        ->actions([
            Tables\Actions\ActionGroup::make([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]),
        ])
        ->bulkActions([
            //Tables\Actions\DeleteBulkAction::make(),
        ]);
    }
}
