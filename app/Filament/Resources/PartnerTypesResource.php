<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PartnerTypesResource\Pages;
use App\Filament\Resources\PartnerTypesResource\RelationManagers;
use App\Models\PartnerType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;

// 👇 IMPORTS para INFOLIST
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section as InfoSection;
use Filament\Infolists\Components\Grid as InfoGrid;
use Filament\Infolists\Components\TextEntry;

class PartnerTypesResource extends Resource
{
    protected static ?string $model = PartnerType::class;
    protected static ?string $navigationIcon = 'heroicon-o-minus';
    protected static ?string $navigationGroup = 'Resources';
    protected static ?int    $navigationSort  = 7;   // aparecerá primero

    /* ───── NUEVO: burbuja con el total en el menú ───── */
    public static function getNavigationBadge(): ?string
    {
        return PartnerType::count();
    }

    public static function canCreate(): bool
    {
        // Devuelve false para ocultar el botón “New country”
        return true;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
                Section::make('Partner Type Details')
                ->columns(1)    // ← aquí defines dos columnas
                ->schema([

                    TextInput::make('name')
                    ->label('Name')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255)
                    ->afterStateUpdated(fn ($state, callable $set) => $set('name', ucwords(strtolower($state)))),
                    //->helperText('First letter of each word will be capitalised.')
                    //->disabled()
                    //->dehydrated(false),   // evita que el valor se envíe al servidor

                    TextInput::make('acronym')
                    ->label('Acronym')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(3)
                    ->rule('regex:/^[A-Z]+$/')
                    ->afterStateUpdated(fn ($state, callable $set) => $set('acronym', strtoupper($state))),
                    //->helperText('Only uppercase letters allowed.')
                    //->disabled()
                    //->dehydrated(false),   // evita que el valor se envíe al servidor

                    Textarea::make('description')
                    ->label('Description')
                    ->required()
                    ->columnSpan('full')
                    ->autosize()
                    ->afterStateUpdated(fn ($state, callable $set) => $set('description', ucfirst(strtolower($state))))
                    ->helperText('Please provide a brief description of the partner type.'),
                    //->helperText('Please provide a brief description of the sector. Only the first letter will be capitalised.')
                    //->disabled()
                    //->dehydrated(false),   // evita que el valor se envíe al servidor

                ])
                ->maxWidth('5xl')
                ->collapsible(),

            ]);
    }



public static function infolist(Infolist $infolist): Infolist
{
    return $infolist->schema([
        /* ─────────────────────────  PROFILE  ───────────────────────── */
        InfoSection::make('Partner Type Profile')->schema([
            InfoGrid::make(1)
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

                    // Acronym
                    InfoGrid::make(12)
                        ->extraAttributes(['style' => 'border-bottom:1px solid rgba(255,255,255,0.12); padding:2px 0;'])
                        ->schema([
                            TextEntry::make('acr_label')
                                ->label('')
                                ->state('Acronym:')
                                ->weight('bold')
                                ->alignment('right')
                                ->columnSpan(3),
                            TextEntry::make('acr_value')
                                ->label('')
                                ->state(fn ($record) => $record->acronym ? strtoupper($record->acronym) : '—')
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
                                ->columnSpan(9),
                        ]),
                ]),
        ])
        ->maxWidth('5xl')
        ->collapsible(),

        /* ─────────────────────────  AUDIT  ───────────────────────── */
       /*  InfoSection::make('Audit Dates')->schema([
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
            ->recordUrl(fn (PartnerType $record) => static::getUrl('view', ['record' => $record]))
            ->columns([
                //
                TextColumn::make('id')
                    ->sortable(),
                TextColumn::make('name')
                    ->sortable(),
                TextColumn::make('acronym')
                    ->searchable()
                    ->sortable(),
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
            'index' => Pages\ListPartnerTypes::route('/'),
            'create' => Pages\CreatePartnerTypes::route('/create'),
            'view'   => Pages\ViewPartnerTypes::route('/{record}'),   // 👈 NUEVA
            'edit' => Pages\EditPartnerTypes::route('/{record}/edit'),
        ];
    }
}
