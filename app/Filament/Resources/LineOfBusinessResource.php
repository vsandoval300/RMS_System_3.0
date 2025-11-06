<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LineOfBusinessResource\Pages;
use App\Filament\Resources\LineOfBusinessResource\RelationManagers;
use App\Models\LineOfBusiness;
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
use Illuminate\Database\Eloquent\Model;

// 👇 IMPORTS para INFOLIST
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section as InfoSection;
use Filament\Infolists\Components\Grid as InfoGrid;
use Filament\Infolists\Components\TextEntry;

class LineOfBusinessResource extends Resource
{
    protected static ?string $model = LineOfBusiness::class;
    protected static ?string $navigationIcon = 'heroicon-o-minus';
    protected static ?string $navigationLabel = 'Lines of Businesses';
    protected static ?string $navigationGroup = 'Underwritten';
    protected static ?int    $navigationSort  = 4;   // aparecerá primero

    /* ───── NUEVO: burbuja con el total en el menú ───── */
    public static function getNavigationBadge(): ?string
    {
        // Puedes usar self::$model::count() o Reinsurer::count()
        return LineOfBusiness::count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
                Section::make('Line of Business')
                ->columns(1)    // ← aquí defines dos columnas
                ->schema([

                    TextInput::make('name')
                    ->label('Name')
                    ->required()
                    ->unique(ignoreRecord: true, column: 'name')   // 👈 ignora el registro en edición
                    ->maxLength(255)
                    ->afterStateUpdated(fn ($state, callable $set) => $set('name', ucwords(strtolower($state))))
                    ->helperText('First letter of each word will be capitalised.'),
                    //->extraAttributes(['class' => 'w-1/2']),

                    Textarea::make('description')
                    ->label('Description')
                    ->required()
                    ->columnSpan('full')
                    ->autosize()
                    ->afterStateUpdated(fn ($state, callable $set) => $set('description', ucfirst(strtolower($state))))
                    ->helperText('Please provide a brief description of the sector. Only the first letter will be capitalised.'),
                    //->extraAttributes(['class' => 'w-1/2']),

                    Select::make('risk_covered')
                    ->label('Risk Covered')
                    ->placeholder('Select the risk covered.') // 👈 Aquí cambias el texto
                    ->options([
                        'Life' => 'Life',
                        'Non-Life' => 'Non-Life',
                    ])
                    ->required()
                    ->searchable(),  

                ]),
            ]);
    }




    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            /* ─────────────────────────  PROFILE  ───────────────────────── */
            InfoSection::make('Line of Business Profile')->schema([
                InfoGrid::make(2)
                    ->extraAttributes(['style' => 'gap: 6px;'])
                    ->schema([
                        // Filas “Label (3) + Value (9)”
                        InfoGrid::make(1)
                            ->columnSpan(2)
                            ->extraAttributes(['style' => 'row-gap: 0;'])
                            ->schema([

                                // Name
                                InfoGrid::make(12)
                                    ->extraAttributes(['style' => 'border-bottom:1px solid rgba(255,255,255,0.12); padding:2px 0;'])
                                    ->schema([
                                        TextEntry::make('name_label')->label('')->state('Name:')
                                            ->weight('bold')->alignment('right')->columnSpan(3),
                                        TextEntry::make('name_value')->label('')
                                            ->state(fn ($record) => $record->name ?: '—')
                                            ->columnSpan(9),
                                    ]),

                                // Description (multilínea)
                                InfoGrid::make(12)
                                    ->extraAttributes(['style' => 'border-bottom:1px solid rgba(255,255,255,0.12); padding:2px 0;'])
                                    ->schema([
                                        TextEntry::make('desc_label')->label('')->state('Description:')
                                            ->weight('bold')->alignment('right')->columnSpan(3),
                                        TextEntry::make('desc_value')->label('')
                                            ->state(fn ($record) => $record->description ?: '—')
                                            ->extraAttributes(['style' => 'line-height:1.35;'])
                                            ->columnSpan(9),
                                    ]),

                                // Risk Covered
                                InfoGrid::make(12)
                                    ->extraAttributes(['style' => 'border-bottom:1px solid rgba(255,255,255,0.12); padding:2px 0;'])
                                    ->schema([
                                        TextEntry::make('risk_covered')->label('')->state('Risk covered:')
                                            ->weight('bold')->alignment('right')->columnSpan(3),
                                        TextEntry::make('risk_covered')->label('')
                                            ->state(fn ($record) => $record->risk_covered ?: '—')
                                            ->columnSpan(9),
                                    ]),


                            ]),
                    ]),
            ])
            ->maxWidth('5xl')
            ->collapsible(),

            /* ─────────────────────────  AUDIT  ───────────────────────── */
            InfoSection::make('Audit Dates')->schema([
                InfoGrid::make(2)
                    ->extraAttributes(['style' => 'gap: 12px;'])
                    ->schema([
                        InfoGrid::make(12)
                            ->extraAttributes(['style' => 'border-bottom:1px solid rgba(255,255,255,0.12); padding:2px 0;'])
                            ->schema([
                                TextEntry::make('created_label')->label('')->state('Created At:')
                                    ->weight('bold')->alignment('right')->columnSpan(3),
                                TextEntry::make('created_value')->label('')
                                    ->state(fn ($record) => $record->created_at?->format('Y-m-d H:i') ?: '—')
                                    ->columnSpan(9),
                            ]),
                        InfoGrid::make(12)
                            ->extraAttributes(['style' => 'border-bottom:1px solid rgba(255,255,255,0.12); padding:2px 0;'])
                            ->schema([
                                TextEntry::make('updated_label')->label('')->state('Updated At:')
                                    ->weight('bold')->alignment('right')->columnSpan(3),
                                TextEntry::make('updated_value')->label('')
                                    ->state(fn ($record) => $record->updated_at?->format('Y-m-d H:i') ?: '—')
                                    ->columnSpan(9),
                            ]),
                    ]),
            ])
            ->maxWidth('5xl')
            ->compact(),
        ]);
    }










    public static function table(Table $table): Table
    {
        return $table
            ->recordUrl(fn (LineOfBusiness $record) => static::getUrl('view', ['record' => $record]))
            ->columns([
                //
                TextColumn::make('id')
                    ->sortable(),
                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->extraAttributes([
                        'style' => 'width: 180px; white-space: normal;', // ✅ Deja que el texto se envuelva
                    ]),
                TextColumn::make('description')
                    ->label('Description')
                    ->wrap()
                    ->extraAttributes([
                        'style' => 'width: 600px; white-space: normal;', // ancho fijo de 300px
                    ]),
                TextColumn::make('risk_covered')
                    ->label('Risk covered')
                    ->wrap()
                    ->extraAttributes([
                        'style' => 'width: 100px; white-space: normal;', // ancho fijo de 300px
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
            'index' => Pages\ListLineOfBusinesses::route('/'),
            'create' => Pages\CreateLineOfBusiness::route('/create'),
            'view'   => Pages\ViewLineOfBusiness::route('/{record}'),   // 👈 NUEVA
            'edit' => Pages\EditLineOfBusiness::route('/{record}/edit'),
        ];
    }
}
