<?php

namespace App\Filament\Resources\Managers;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Actions\ActionGroup;
use Filament\Actions\ViewAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Resources\Managers\Pages\ListManagers;
use App\Filament\Resources\Managers\Pages\CreateManager;
use App\Filament\Resources\Managers\Pages\ViewManager;
use App\Filament\Resources\Managers\Pages\EditManager;
use App\Filament\Resources\ManagerResource\Pages;
use App\Filament\Resources\ManagerResource\RelationManagers;
use App\Models\Manager;
use App\Models\Country;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Grid;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\Textarea;
use Filament\Support\Enums\VerticalAlignment;
use Illuminate\Validation\Rules\Unique;
use Filament\Infolists\Components\TextEntry;
use Illuminate\Database\Eloquent\Model;

class ManagerResource extends Resource
{
    protected static ?string $model = Manager::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-minus';
     protected static string | \UnitEnum | null $navigationGroup = 'Resources';
     protected static ?int    $navigationSort  = 10;   // aparecerá primero

   /* ───── NUEVO: burbuja con el total en el menú ───── */
    public static function getNavigationBadge(): ?string
    {
        // Puedes usar self::$model::count() o Reinsurer::count()
        return Manager::count();
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                //
                Section::make('Manager Details')
                ->columns(1)    // ← aquí defines dos columnas
                ->columnSpanFull()
                ->schema([

                    TextInput::make('name')
                        ->label('Name')
                        ->required()
                        ->unique(
                            ignoreRecord: true,
                            modifyRuleUsing: fn (Unique $rule) => $rule->whereNull('deleted_at')
                        )
                        ->live(onBlur: false)
                        ->maxLength(255)
                        ->afterStateUpdated(fn ($state, callable $set) => $set('name', ucwords(strtolower($state))))
                        ->extraAttributes(['class' => 'w-1/2'])
                        ->helperText('First letter of each word will be capitalised.'),
                        //->extraAttributes(['class' => 'w-1/2']),

                    Textarea::make('address')
                        ->label('Address')
                        ->placeholder('Please provide manager address')
                        ->required()
                        ->columnSpan('full')
                        ->afterStateUpdated(fn ($state, callable $set) => $set('address', ucfirst(strtolower($state))))
                        ->extraAttributes(['class' => 'w-1/2']),

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
                        ->placeholder('Select a country')
                        ->extraAttributes(['class' => 'w-1/2']),
                        //->helperText('Choose the reinsurer\'s country.'),
                           
                //
                ]),
            ]);
    }



public static function infolist(Schema $schema): Schema
{
    return $schema->components([
        /* ─────────────────────────  PROFILE  ───────────────────────── */
        Section::make('Manager Profile')
        ->columnSpanFull()
        ->schema([
            \Filament\Schemas\Components\Grid::make(1)
                ->extraAttributes(['style' => 'row-gap: 0;'])
                ->schema([

                    // Name
                    \Filament\Schemas\Components\Grid::make(12)
                        ->extraAttributes(['style' => 'border-bottom:1px solid rgba(255,255,255,0.12); padding:2px 0;'])
                        ->schema([
                                TextEntry::make('name_label')
                                    ->hiddenLabel()
                                    ->state('Name:')
                                    ->weight('bold')
                                    ->alignment('right')
                                    ->columnSpan(3),
                                TextEntry::make('name_value')
                                    ->hiddenLabel()
                                    ->state(fn ($record) => $record->name ?: '—')
                                    ->columnSpan(9),
                            ]),
                            \Filament\Schemas\Components\Grid::make(12)
                                ->extraAttributes(['style' => 'border-bottom:1px solid rgba(255,255,255,0.12); padding:2px 0;'])
                                    ->schema([
                                        TextEntry::make('address_label')
                                            ->hiddenLabel()
                                            ->state('Address:')
                                            ->weight('bold')
                                            ->alignment('right')
                                            ->columnSpan(3),
                                        TextEntry::make('address_value')
                                            ->hiddenLabel()
                                            ->state(fn ($record) => $record->address ?: '—')
                                            ->columnSpan(9),
                                        ]),
                            \Filament\Schemas\Components\Grid::make(12)
                                ->extraAttributes(['style' => 'border-bottom:1px solid rgba(255,255,255,0.12); padding:2px 0;'])
                                    ->schema([

                                        TextEntry::make('country_label')
                                            ->hiddenLabel()
                                            ->state('Country:')
                                            ->weight('bold')
                                            ->alignment('right')
                                            ->columnSpan(3),
                                        TextEntry::make('country_value')
                                            ->hiddenLabel()
                                            ->state(fn ($record) => $record->country
                                                ? "{$record->country->alpha_3} - {$record->country->name}"
                                                : '—'
                                            )
                                            ->columnSpan(9),
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
            ->recordUrl(fn (Manager $record) => static::getUrl('view', ['record' => $record]))
            ->columns([
                //
                TextColumn::make('id')
                    ->sortable(),
                TextColumn::make('name')
                    ->searchable()  
                    ->sortable()
                    ->extraAttributes([
                        'style' => 'width: 320px; white-space: normal;', // ✅ Deja que el texto se envuelva
                    ]),

                TextColumn::make('address')
                    ->searchable()  
                    ->sortable()
                    ->extraAttributes([
                        'style' => 'width: 600px; white-space: normal;', // ✅ Deja que el texto se envuelva
                    ]),   
                    
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
            'index' => ListManagers::route('/'),
            'create' => CreateManager::route('/create'),
            'view'   => ViewManager::route('/{record}'),   // 👈 NUEVA
            'edit' => EditManager::route('/{record}/edit'),
        ];
    }
}
