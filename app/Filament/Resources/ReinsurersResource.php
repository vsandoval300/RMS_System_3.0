<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReinsurersResource\Pages;
use App\Filament\Resources\ReinsurersResource\RelationManagers;
use App\Models\Country;
use App\Models\Reinsurer;
use App\Models\Manager;
use App\Models\OperativeStatus;
use App\Models\ReinsurerType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\FileUpload;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Url;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile; // Livewire v3
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;
use Filament\Tables\Columns\TextColumn;





class ReinsurersResource extends Resource
{
    protected static ?string $model = Reinsurer::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Reinsurers';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
                Section::make('Details')
                ->columns(2)    // ← aquí defines dos columnas
                ->schema([

                    TextInput::make('id')
                    ->label('ID')
                    ->readOnly()
                    ->disabled(), // ❗️Esto lo hace visualmente "gris" y no editable
                    
                    TextInput::make('cns_reinsurer')
                    ->label('LSK (Legacy Substitute Key)')
                    ->unique()
                    ->nullable(),
                    
                    TextInput::make('name')
                    ->label('Name')
                    ->required()
                    ->unique()
                    ->maxLength(255)
                    ->afterStateUpdated(fn ($state, callable $set) => $set('name', ucwords(strtolower($state))))
                    ->helperText('First letter of each word will be capitalised.'),
                    
                    TextInput::make('short_name')
                    ->label('Short Name')
                    ->unique()
                    ->required()
                    ->maxLength(255)
                    ->afterStateUpdated(fn ($state, callable $set) => $set('short_name', ucwords(strtolower($state))))
                    ->helperText('First letter of each word will be capitalised.'),
                    
                    TextInput::make('acronym')
                    ->label('Acronym')
                    ->required()
                    ->unique()
                    ->maxLength(3)
                    ->rule('regex:/^[A-Z]+$/')
                    ->afterStateUpdated(fn ($state, callable $set) => $set('acronym', strtoupper($state)))
                    ->helperText('Only uppercase letters allowed.'),
                    
                    Select::make('parent_id')
                    ->label('Parent')
                    ->relationship(
                        name: 'parent',          // nombre de la relación belongsTo
                        titleAttribute: 'name', // el campo que Filament usará en la consulta
                        modifyQueryUsing: fn (Builder $query) => $query->orderBy('name'), // ← ordena A-Z
                    )
                    ->searchable()
                    ->preload()
                    ->nullable()
                    ->helperText('Select the parent reinsurer if applicable.'),
                    
                    Select::make('class')
                    ->label('Class')
                    ->options([
                        'Class 1' => 'Class 1',
                        'Class 2' => 'Class 2',
                    ])
                    ->required()
                    ->searchable()
                    ->helperText('Select the reinsurer class.'),
                    
                    TextInput::make('established')
                    ->label('Established Year')
                    ->numeric()
                    ->step(1)                         // opcional: avanza de uno en uno
                    ->minValue(2010)                  // límite inferior fijo
                    ->maxValue(fn () => now()->year)  // límite superior dinámico (2025, 2026, …)
                    ->rules([
                        'required',
                        'integer',
                        'between:2010,' . now()->year, // refuerza la validación en el backend
                    ])
                    //->live(onBlur: true)  // evita validar en cada tecla; valida al perder foco
                    ->placeholder('e.g. 2015')
                    ->helperText('Enter a 4-digit year between 2010 and ' . now()->year . '.')
                    ->required(),
                    
                    Select::make('manager_id')
                    ->label('Manager')
                    ->relationship('manager','name')
                    //->options(function () {
                    //    return Manager::orderBy('name')->pluck('name', 'id');
                    //})
                    ->searchable()
                    ->preload()
                    ->required()
                    ->helperText('Select the manager assigned to this reinsurer.')
                    ->placeholder('Select a manager'),
                    
                    Select::make('country_id')
                    ->label('Country')
                    ->relationship(
                        name: 'country',          // nombre de la relación belongsTo
                        titleAttribute: 'alpha_3', // el campo que Filament usará en la consulta
                        modifyQueryUsing: fn (Builder $query) => $query->orderBy('alpha_3'), // ← ordena A-Z
                    )
                    ->getOptionLabelFromRecordUsing(      // <- aquí personalizas la etiqueta
                        fn (Country $record) => "{$record->alpha_3} - {$record->name}"
                    )
                    ->searchable()
                    ->preload()
                    ->required()
                    ->placeholder('Select a country')
                    ->helperText('Choose the reinsurer\'s country.'),
                    
                    Select::make('reinsurer_type_id')
                    ->label('Type')
                    ->relationship(
                        name: 'reinsurer_type',          // nombre de la relación belongsTo
                        titleAttribute: 'type_acronym' // el campo que Filament usará en la consulta
                    )
                    ->getOptionLabelFromRecordUsing(      // <- aquí personalizas la etiqueta
                        fn (ReinsurerType $record) => "{$record->type_acronym} - {$record->description}"
                    )
                    ->searchable()
                    ->preload()
                    ->required()
                    ->placeholder('Select reinsurer type')
                    ->helperText('Choose the type of reinsurer.'),
                    
                    Select::make('operative_status_id')
                    ->label('Operative Status')
                    ->relationship(
                        name: 'operative_status',          // nombre de la relación belongsTo
                        titleAttribute: 'acronym', // el campo que Filament usará en la consulta
                        modifyQueryUsing: fn (Builder $query) => $query->orderBy('acronym'), // ← ordena A-Z
                    )
                    ->getOptionLabelFromRecordUsing(      // <- aquí personalizas la etiqueta
                        fn (OperativeStatus $record) => "{$record->acronym} - {$record->description}"
                    )
                    ->searchable()
                    ->preload()
                    ->required()
                    ->placeholder('Select operative status')
                    ->helperText('Choose the reinsurer’s current operative status.'),
                    

                ]),

                   Section::make('Images')
                    ->columns(2)
                    
                        ->schema([

                            //====================================  
                            // LOGO
                            //====================================
                            FileUpload::make('logo')
                                ->label('Logo')
                                ->disk('s3')
                                ->directory('reinsurers/logos')
                                ->visibility('public')
                                ->image()
                                //->imagePreviewHeight('100')
                                ->previewable(),
                            //====================================
                            // ICON
                            //====================================
                            FileUpload::make('icon')
                                ->label('Icon')
                                ->disk('s3')
                                ->directory('reinsurers/icons')
                                ->visibility('public')
                                ->image()
                                //->imagePreviewHeight('100')
                                ->previewable(),
                            //====================================                       

                        ]),   // ← cierra schema() y luego la Sección
                  
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







                /*
                
                TextColumn::make('short_name')
                ->label('Name')
                ->html()
                ->formatStateUsing(function ($state, $record) {
                    $iconPath   = $record->icon;       // puede ser URL o ruta relativa
                    $shortName  = $record->short_name; // texto a mostrar

                    // Si no hay icono, simplemente devuelve el nombre
                    if (blank($iconPath)) {
                        return "<span>{$shortName}</span>";
                    }

                    // Determina la URL final del icono
                    $iconUrl = Str::startsWith($iconPath, ['http://', 'https://'])
                        ? $iconPath                                   // ya es URL completa
                        : Storage::disk('s3')->url($iconPath);        // convierte ruta relativa

                    return "<div style='display:flex;align-items:center;gap:8px;'>
                                <img src='{$iconUrl}'
                                    alt='icon'
                                    style='width:24px;height:24px;border-radius:50%;object-fit:cover;' />
                                <span>{$shortName}</span>
                            </div>";
                }),
                /*
                TextColumn::make('short_name')
                ->label('Name')
                ->html()
                ->formatStateUsing(function ($record) {
                    $icon = $record->icon ?? '';
                    $shortName = $record->short_name ?? '';

                    // Si no hay icono, muestra solo el nombre corto
                    if (!$icon) {
                        return "<span>{$shortName}</span>";
                    }

                    return "<div style='display: flex; align-items: center; gap: 8px;'>
                                <img src='{$icon}' alt='icon' style='width: 24px; height: 24px; border-radius: 50%; object-fit: cover;' />
                                <span>{$shortName}</span>
                            </div>";
                })
                ->sortable()
                ->searchable(),
                */
                
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


            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
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
            
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListReinsurers::route('/'),
            'create' => Pages\CreateReinsurers::route('/create'),
            'edit' => Pages\EditReinsurers::route('/{record}/edit'),
        ];
    }
}
