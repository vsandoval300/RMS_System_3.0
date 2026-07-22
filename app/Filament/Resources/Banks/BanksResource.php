<?php

namespace App\Filament\Resources\Banks;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Actions\CreateAction;
use Filament\Actions\ActionGroup;
use Filament\Actions\ViewAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Resources\Banks\Pages\ListBanks;
use App\Filament\Resources\Banks\Pages\CreateBanks;
use App\Filament\Resources\Banks\Pages\ViewBanks;
use App\Filament\Resources\Banks\Pages\EditBanks;
use App\Filament\Resources\BanksResource\Pages;
use App\Filament\Resources\BanksResource\RelationManagers;
use App\Models\Bank;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Validation\Rules\Unique;
use Filament\Infolists\Components\TextEntry;

class BanksResource extends Resource
{
    protected static ?string $model = Bank::class;
    // protected static ?string $cluster = Resources::class; // ✅ Vinculación correcta
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-minus';
    //protected static ?string $cluster = \App\Filament\Clusters\Resources::class;
    protected static string | \UnitEnum | null $navigationGroup = 'Banks';
    protected static ?int    $navigationSort  = 1;   // aparecerá primero

    /* ───── NUEVO: burbuja con el total en el menú ───── */
    public static function getNavigationBadge(): ?string
    {
        // Puedes usar self::$model::count() o Reinsurer::count()
        return Bank::count();
    }

    /*--------------------------------------------------------------
     | 1. Form New and Edit
     --------------------------------------------------------------*/
    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make() //Grupo 1
                ->columnSpanFull()
                ->schema([
                    Section::make('Bank Details')
                    ->schema([
                    
                        TextInput::make('name')
                            ->label('Name')
                            ->placeholder('Please provide name')
                            ->required()
                            ->unique(
                                ignoreRecord: true,
                                modifyRuleUsing: fn (Unique $rule) => $rule->whereNull('deleted_at')
                            )
                            ->maxLength(255),
                            
                        Textarea::make('address')
                            ->label('Address')
                            ->placeholder('Please provide bank address')
                            ->required(),
                            
                        TextInput::make('aba_number')
                            ->label('ABA number')
                            ->placeholder('Please provide ABA number.')
                            ->rule('digits:9') 
                            ->unique(
                                ignoreRecord: true,
                                modifyRuleUsing: fn (Unique $rule) => $rule->whereNull('deleted_at')
                            )
                            ->maxLength(255)
                            ->helperText(fn (string $context) => in_array($context, ['create', 'edit']) 
                                ? '9 digits, e.g. 123456789' 
                                : null),
                            
                        TextInput::make('swift_code')
                            ->label('SWIFT Code')
                            ->placeholder('Please provide SWIFT code.')
                            ->required()
                            ->rule('regex:/^[A-Z0-9]{8}([A-Z0-9]{3})?$/') // 8 o 11 caracteres alfanuméricos
                            ->unique(
                                ignoreRecord: true,
                                modifyRuleUsing: fn (Unique $rule) => $rule->whereNull('deleted_at')
                            )
                            ->maxLength(11) // 👈 opcional: restringir a máximo 11 chars
                            ->afterStateUpdated(fn ($state, callable $set) => $set('swift_code', strtoupper($state)))
                            ->extraAttributes(['style' => 'text-transform:uppercase'])
                            ->helperText(fn (string $context) => in_array($context, ['create', 'edit']) 
                                ? '8 or 11 characters, e.g. DEUTDEFF500' 
                                : null), 
                               
                    ])
                    ->columns(2),
                ])
                ->columnSpanFull(),
            ]);
    }




    /*--------------------------------------------------------------
     | 2. Infolist
    --------------------------------------------------------------*/
    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            /* ─────────────────────────  PROFILE  ───────────────────────── */
            Section::make('Bank Profile')
            ->columnSpanFull()
            ->schema([
                \Filament\Schemas\Components\Grid::make(1)
                    ->extraAttributes(['style' => 'row-gap: 0;'])
                    ->schema([

                        // Name
                        \Filament\Schemas\Components\Grid::make(12)
                            ->extraAttributes([
                                'style' => 'border-bottom:1px solid rgba(255,255,255,0.12); padding:2px 0;',
                            ])
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

                        // Address
                        \Filament\Schemas\Components\Grid::make(12)
                            ->extraAttributes([
                                'style' => 'border-bottom:1px solid rgba(255,255,255,0.12); padding:2px 0;',
                            ])
                            ->schema([
                                TextEntry::make('addr_label')
                                    ->hiddenLabel()
                                    ->state('Address:')
                                    ->weight('bold')
                                    ->alignment('right')
                                    ->columnSpan(3),
                                TextEntry::make('addr_value')
                                    ->hiddenLabel()
                                    ->state(fn ($record) => $record->address ?: '—')
                                    ->columnSpan(9),
                            ]),

                        // ABA Number
                        \Filament\Schemas\Components\Grid::make(12)
                            ->extraAttributes([
                                'style' => 'border-bottom:1px solid rgba(255,255,255,0.12); padding:2px 0;',
                            ])
                            ->schema([
                                TextEntry::make('aba_label')
                                    ->hiddenLabel()
                                    ->state('ABA Number:')
                                    ->weight('bold')
                                    ->alignment('right')
                                    ->columnSpan(3),
                                TextEntry::make('aba_value')
                                    ->hiddenLabel()
                                    ->state(fn ($record) => $record->aba_number ?: '—')
                                    ->extraAttributes(['style' => 'font-family: ui-monospace, SFMono-Regular, Menlo, monospace;'])
                                    ->columnSpan(9),
                            ]),

                        // SWIFT Code
                        \Filament\Schemas\Components\Grid::make(12)
                            ->extraAttributes([
                                'style' => 'border-bottom:1px solid rgba(255,255,255,0.12); padding:2px 0;',
                            ])
                            ->schema([
                                TextEntry::make('swift_label')
                                    ->hiddenLabel()
                                    ->state('SWIFT Code:')
                                    ->weight('bold')
                                    ->alignment('right')
                                    ->columnSpan(3),
                                TextEntry::make('swift_value')
                                    ->hiddenLabel()
                                    ->state(fn ($record) => $record->swift_code ? strtoupper($record->swift_code) : '—')
                                    ->extraAttributes(['style' => 'font-family: ui-monospace, SFMono-Regular, Menlo, monospace; letter-spacing:0.5px;'])
                                    ->columnSpan(9),
                            ]),
                    ]),
            ])
            ->maxWidth('6xl')
            ->collapsible(),

            /* ─────────────────────────  AUDIT  ───────────────────────── */
            /* InfoSection::make('Audit Dates')->schema([
                InfoGrid::make(12)
                    ->extraAttributes(['style' => 'border-bottom:1px solid rgba(255,255,255,0.12); padding:2px 0;'])
                    ->schema([
                        TextEntry::make('created_label')
                            ->label('')->state('Created At:')->weight('bold')
                            ->alignment('right')->columnSpan(3),
                        TextEntry::make('created_value')
                            ->label('')
                            ->state(fn ($record) => $record->created_at?->format('Y-m-d H:i') ?: '—')
                            ->columnSpan(9),
                    ]),
                InfoGrid::make(12)
                    ->extraAttributes(['style' => 'border-bottom:1px solid rgba(255,255,255,0.12); padding:2px 0;'])
                    ->schema([
                        TextEntry::make('updated_label')
                            ->label('')->state('Updated At:')->weight('bold')
                            ->alignment('right')->columnSpan(3),
                        TextEntry::make('updated_value')
                            ->label('')
                            ->state(fn ($record) => $record->updated_at?->format('Y-m-d H:i') ?: '—')
                            ->columnSpan(9),
                    ]),
            ])
            ->maxWidth('6xl')
            ->compact(), */
        ]);
    }



    /*--------------------------------------------------------------
     | 3. CRUD Table
     --------------------------------------------------------------*/
    public static function table(Table $table): Table
    {
        return $table
            ->recordUrl(fn (Bank $record) => static::getUrl('view', ['record' => $record]))
            ->columns([
                //
                TextColumn::make('id')
                ->sortable(),
                TextColumn::make('name')
                ->searchable()
                ->sortable(),
                TextColumn::make('address')
                    ->label('Address')
                    ->sortable()
                    ->searchable()
                    ->wrap() // ✅ Permite que se haga multilínea
                    ->extraAttributes([
                        'class' => 'max-w-xl whitespace-normal', // ✅ Deja que el texto se envuelva
                    ]),
                TextColumn::make('aba_number')
                ->searchable()
                ->sortable(),
                TextColumn::make('swift_code')
                ->searchable()
                ->sortable(),



            ])
            ->filters([
                //
            ])

            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),

                    EditAction::make()
                        ->closeModalByClickingAway(false)
                        ->closeModalByEscaping(false),

                    DeleteAction::make(),
                ]),
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
            'index' => ListBanks::route('/'),
            'create' => CreateBanks::route('/create'),
            'view'   => ViewBanks::route('/{record}'),   // 👈 NUEVA
            'edit' => EditBanks::route('/{record}/edit'),
        ];
    }
}
