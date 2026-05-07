<?php

namespace App\Filament\Resources\Industries;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Actions\ActionGroup;
use Filament\Actions\ViewAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Resources\Industries\Pages\ListIndustries;
use App\Filament\Resources\Industries\Pages\CreateIndustries;
use App\Filament\Resources\Industries\Pages\ViewIndustries;
use App\Filament\Resources\Industries\Pages\EditIndustries;
use App\Filament\Resources\IndustryResource\Pages;
use App\Filament\Resources\SectorsResource\RelationManagers;
use App\Models\Industry;
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
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Validation\Rules\Unique;
use Filament\Infolists\Components\TextEntry;




class IndustryResource extends Resource
{
    protected static ?string $model = Industry::class;
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-minus';
     protected static ?string $navigationLabel = 'Industries';
    protected static string | \UnitEnum | null $navigationGroup = 'Resources';
    protected static ?int    $navigationSort  = 5;   // aparecerá primero

    /* ───── NUEVO: burbuja con el total en el menú ───── */
    public static function getNavigationBadge(): ?string
    {
        // Puedes usar self::$model::count() o Reinsurer::count()
        return Industry::count();
    }

    /* public static function canCreate(): bool
    {
        // Devuelve false para ocultar el botón “New country”
        return false;
    } */
    
   


public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                //
                Section::make('Industry Details')
                ->columns(1)    // ← aquí defines dos columnas
                ->schema([


                    TextInput::make('name')
                        ->label('Name')
                        ->required()
                        ->unique(
                            ignoreRecord: true,
                            modifyRuleUsing: fn (Unique $rule) => $rule->whereNull('deleted_at')
                        )
                        ->maxLength(255)
                        ->afterStateUpdated(fn ($state, callable $set) => $set('name', ucwords(strtolower($state))))
                        ->extraAttributes(['class' => 'w-1/2']),    

                    Textarea::make('description')
                        ->label('Description')
                        ->required()
                        ->autosize()
                        ->afterStateUpdated(fn ($state, callable $set) => $set('description', ucfirst(strtolower($state))))
                        ->helperText('Please provide a brief description of the industry.')
                        ->extraAttributes(['class' => 'w-1/2']),
                        
                ]),


            ]);
    }








    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            /* ─────────────────────────  PROFILE  ───────────────────────── */
            Section::make('Industry Profile')->schema([
                \Filament\Schemas\Components\Grid::make(1)
                    ->extraAttributes(['style' => 'row-gap: 0;'])
                    ->schema([

                        // Name  (Label 3 / Value 9)
                        \Filament\Schemas\Components\Grid::make(12)
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

                        // Description  (Label 3 / Value 9)
                        \Filament\Schemas\Components\Grid::make(12)
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
                                    ->columnSpan(9),
                            ]),
                    ]),
            ])
            ->maxWidth('5xl')
            ->collapsible(),

            /* ─────────────────────────  AUDIT  ───────────────────────── */
            /* InfoSection::make('Audit Dates')
                ->schema([
                    InfoGrid::make(12)
                        ->extraAttributes(['style' => 'border-bottom:1px solid rgba(255,255,255,0.12); padding:2px 0;'])
                        ->schema([
                            TextEntry::make('created_label')
                                ->label('')
                                ->state('Created At:')
                                ->weight('bold')
                                ->alignment('right')
                                ->columnSpan(3),
                            TextEntry::make('created_value')
                                ->label('')
                                ->state(fn ($record) => $record->created_at?->format('Y-m-d H:i') ?: '—')
                                ->columnSpan(9),
                        ]),
                    InfoGrid::make(12)
                        ->extraAttributes(['style' => 'border-bottom:1px solid rgba(255,255,255,0.12); padding:2px 0;'])
                        ->schema([
                            TextEntry::make('updated_label')
                                ->label('')
                                ->state('Updated At:')
                                ->weight('bold')
                                ->alignment('right')
                                ->columnSpan(3),
                            TextEntry::make('updated_value')
                                ->label('')
                                ->state(fn ($record) => $record->updated_at?->format('Y-m-d H:i') ?: '—')
                                ->columnSpan(9),
                        ]),
                ])
                ->maxWidth('5xl')
                ->compact(), */
        ]);
    }






    public static function table(Table $table): Table
    {
        return $table
        ->recordUrl(fn (Industry $record) => static::getUrl('view', ['record' => $record]))
            ->columns([
                //
                TextColumn::make('id')
                    ->sortable(),
                TextColumn::make('name')
                    ->label('Name')
                    ->sortable()
                    ->searchable()
                    ->extraAttributes([
                        'class' => 'min-w-[12rem] whitespace-nowrap',
                    ]),
                
                TextColumn::make('description')
                    ->label('Description')
                    ->sortable()
                    ->searchable()
                    ->wrap() // ✅ Permite que se haga multilínea
                    ->extraAttributes([
                        'class' => 'max-w-xl whitespace-normal', // ✅ Deja que el texto se envuelva
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
            'index' => ListIndustries::route('/'),
            'create' => CreateIndustries::route('/create'),
            'view'   => ViewIndustries::route('/{record}'),   // 👈 NUEVA
            'edit' => EditIndustries::route('/{record}/edit'),
        ];
    }
}
