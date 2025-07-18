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

class OperativeDocsRelationManager extends RelationManager
{
    protected static string $relationship = 'OperativeDocs';
    protected static ?string $title = 'Operative Documents';
    protected static ?string $recordTitleAttribute = 'description';


    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with('docType'); // <- evita N+1
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            // Primera burbuja: solo Id Document
            Forms\Components\Section::make()
                ->schema([
        Forms\Components\Grid::make(12)
            ->schema([
                Forms\Components\Placeholder::make('')
                    ->columnSpan(6), // deja media fila vacía

                Forms\Components\TextInput::make('id')
                    ->label('Id Document')
                    ->disabled()
                    ->dehydrated() // <- muy importante para que aún así se envíe
                    ->required()
                    ->columnSpan(6),
            ]),
    ])
    ->compact(),

            // Segunda burbuja: el resto de los campos
            Forms\Components\Section::make('Document Details')
                ->schema([
                    // Title ocupa toda la fila
                    Forms\Components\Textarea::make('description')
                        ->label('Tittle')
                        ->maxLength(255)
                        ->columnSpanFull(), // 👈 ocupa ambas columnas (100%)

                    // El resto en 2 columnas
                    Forms\Components\Select::make('operative_doc_type_id')
                        ->label('Document Type')
                        ->relationship('docType', 'name')
                        ->required(),

                    Forms\Components\Toggle::make('client_payment_tracking')
                        ->label('Client Payment Tracking')
                        ->default(false)
                        ->helperText('Include tracking of payments from the
                                     original client if this option is enabled.'),
                    
                    Forms\Components\DatePicker::make('inception_date')
                        ->label('Inception Date')
                        ->required(),

                    Forms\Components\DatePicker::make('expiration_date')
                        ->label('Expiration Date')
                        ->required(),

                    
                ])
                ->columns(2)
                ->compact(),

                // 🟦 Tercera burbuja: solo el archivo
                    Forms\Components\Section::make('File Upload')
                        ->schema([
                            FileUpload::make('document_path')
                                ->label('File')
                                ->disk('s3')
                                ->directory('reinsurers/OperativeDocuments')
                                ->visibility('private')
                                ->acceptedFileTypes(['application/pdf']) // ✅ Solo permite PDF
                                ->helperText('Only PDF files are allowed.'),
                        ])
                        ->compact(),
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
                ->mutateFormDataUsing(function (array $data, $livewire) {
                    // Este callback se ejecuta cuando se guarda el formulario
                    if (! isset($data['id'])) {
                        $business = $livewire->ownerRecord;
                        $count = $business->operativeDocs()->count() + 1;
                        $data['id'] = $business->business_code . '-' . str_pad($count, 2, '0', STR_PAD_LEFT);
                    }
                    return $data;
                })
                ->beforeFormFilled(function ($livewire, $action) {
                    // Esto precarga el campo 'id' antes de abrir el modal
                    $business = $livewire->ownerRecord;
                    $count = $business->operativeDocs()->count() + 1;
                    $generatedId = $business->business_code . '-' . str_pad($count, 2, '0', STR_PAD_LEFT);

                    $action->fillForm([
                        'id' => $generatedId,
                    ]);
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
            Tables\Actions\DeleteBulkAction::make(),
        ]);
    }
}
