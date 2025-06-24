<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReinsurersResource\Pages;
use App\Filament\Resources\ReinsurersResource\RelationManagers;
use App\Models\Countries;
use App\Models\Reinsurers;
use App\Models\Managers;
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
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;
use Illuminate\Support\Facades\Storage;


class ReinsurersResource extends Resource
{
    protected static ?string $model = Reinsurers::class;
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
                    ->label('CNS')
                    ->nullable(),
                    

                    TextInput::make('name')
                    ->label('Name')
                    ->required()
                    ->maxLength(255)
                    ->afterStateUpdated(fn ($state, callable $set) => $set('name', ucwords(strtolower($state))))
                    ->helperText('First letter of each word will be capitalised.'),
                    

                    TextInput::make('short_name')
                    ->label('Short Name')
                    ->required()
                    ->maxLength(255)
                    ->afterStateUpdated(fn ($state, callable $set) => $set('short_name', ucwords(strtolower($state))))
                    ->helperText('First letter of each word will be capitalised.'),
                    

                    TextInput::make('acronym')
                    ->label('Acronym')
                    ->required()
                    ->maxLength(3)
                    ->rule('regex:/^[A-Z]+$/')
                    ->afterStateUpdated(fn ($state, callable $set) => $set('acronym', strtoupper($state)))
                    ->helperText('Only uppercase letters allowed.'),
                    

                    Select::make('parent_id')
                    ->label('Parent')
                    ->options(function () {
                        return Reinsurers::orderBy('name')->pluck('name', 'id');
                    })
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
                    ->minLength(4)
                    ->maxLength(4)
                    ->rules(['regex:/^[12][0-9]{3}$/']) // años entre 1000–2999
                    ->helperText('Enter the 4-digit year the company was established.')
                    ->placeholder('e.g. 1995')
                    ->required(),
                    

                    Select::make('manager_id')
                    ->label('Manager')
                    ->options(function () {
                        return Managers::orderBy('name')->pluck('name', 'id');
                    })
                    ->searchable()
                    ->preload()
                    ->required()
                    ->helperText('Select the manager assigned to this reinsurer.')
                    ->placeholder('Select a manager'),
                    

                    Select::make('country_id')
                    ->label('Country')
                    ->options(function () {
                        return \App\Models\Countries::orderBy('name')
                            ->get()
                            ->mapWithKeys(fn ($country) => [
                                $country->id => "{$country->alpha_3} - {$country->name}"
                            ]);
                    })
                    ->searchable()
                    ->preload()
                    ->required()
                    ->placeholder('Select a country')
                    ->helperText('Choose the reinsurer\'s country.'),
                    

                    Select::make('reinsurer_type_id')
                    ->label('Type')
                    ->options(function () {
                        return \App\Models\ReinsurerType::orderBy('description')->get()
                            ->mapWithKeys(function ($type) {
                                return [$type->id => "{$type->type_acronym} - {$type->description}"];
                            });
                    })
                    ->searchable()
                    ->preload()
                    ->required()
                    ->placeholder('Select reinsurer type')
                    ->helperText('Choose the type of reinsurer.'),
                    

                    Select::make('operative_status_id')
                    ->label('Operative Status')
                    ->options(function () {
                        return \App\Models\OperativeStats::orderBy('description')->get()
                            ->mapWithKeys(function ($status) {
                                return [$status->id => "{$status->acronym} - {$status->description}"];
                            });
                    })
                    ->searchable()
                    ->preload()
                    ->required()
                    ->placeholder('Select operative status')
                    ->helperText('Choose the reinsurer’s current operative status.'),
                    

                ]),

                   Section::make('Images')->schema([

                    FileUpload::make('logo')
                    ->image()->preserveFilenames()
                    ->columnSpan('full')
                    ->required(),

                    FileUpload::make('icon')
                    ->image()->preserveFilenames()
                    ->columnSpan('full')
                    ->required(),

                   
                    
                    
                    
                    
                    
                    
                    
                    
                    
                    
                    
                    /* FileUpload::make('logo')
                    ->label('Logo')
                    ->disk('s3')
                    ->directory('reinsurers/logos')
                    ->image()
                    ->visibility('public')
                    ->default(fn ($record) => $record?->logo)
                    ->imagePreviewHeight('100')
                    ->previewable()
                    ->extraAttributes(['class' => 'w-1/2']),


                    FileUpload::make('icon')
                    ->label('Icon')
                    ->disk('s3')
                    ->directory('reinsurers/icons')
                    ->image()
                    ->visibility('public')
                    ->default(fn ($record) => $record?->icon)
                    ->imagePreviewHeight('100')
                    ->previewable()
                    ->extraAttributes(['class' => 'w-1/2']),

                    */





                ]),    


            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
                Tables\Columns\TextColumn::make('id')->sortable()
                ->extraAttributes([
                        'style' => 'width: 30px; white-space: normal;', // ✅ Deja que el texto se envuelva
                    ]),

                Tables\Columns\TextColumn::make('cns_reinsurer')->sortable()
                ->label('Cns')
                ->extraAttributes([
                        'style' => 'width: 30px; white-space: normal;', // ✅ Deja que el texto se envuelva
                    ]),


                Tables\Columns\TextColumn::make('short_name')
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
                
                Tables\Columns\TextColumn::make('acronym')->searchable()->sortable(),   
                Tables\Columns\TextColumn::make('established')->searchable()->sortable(), 
                Tables\Columns\TextColumn::make('class')->searchable()->sortable(),

                Tables\Columns\TextColumn::make('parent.short_name')
                ->label('Parent')
                ->sortable()
                ->searchable(),

                Tables\Columns\TextColumn::make('reinsurer_type.description')
                ->label('Type')
                ->sortable()
                ->searchable(),

                Tables\Columns\TextColumn::make('country.alpha_3')
                ->label('Country')
                ->sortable()
                ->searchable(),

                Tables\Columns\TextColumn::make('operative_status.description')
                ->label('Operative Status')
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
                    return Countries::whereIn('id', \App\Models\Reinsurers::select('country_id'))
                        ->orderBy('name')
                        ->pluck('name', 'id'); // 'id' como key, 'name' como etiqueta
                })
                ->searchable()
                ->indicator('Country'),


                // ✅ Filtro por la columna Operative Status
                SelectFilter::make('operative_status_id')
                ->label('Operative Status')
                ->options(function () {
                    return \App\Models\OperativeStats::whereIn('id', 
                        \App\Models\Reinsurers::distinct()->pluck('operative_status_id')
                    )->pluck('description', 'id');
                })
                ->searchable()
                ->indicator('Status'),

                // ✅ Filtro por la columna Type
                SelectFilter::make('reinsurer_type_id')
                ->label('Type')
                ->options(function () {
                    return \App\Models\ReinsurerType::whereIn('id', 
                        \App\Models\Reinsurers::distinct()->pluck('reinsurer_type_id')
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
                Tables\Actions\EditAction::make(),
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
