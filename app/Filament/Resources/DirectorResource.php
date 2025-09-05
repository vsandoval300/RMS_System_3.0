<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DirectorResource\Pages;
use App\Models\Country;
use App\Models\Director;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Columns\TextColumn;

// 👇 IMPORTS para INFOLIST
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section as InfoSection;
use Filament\Infolists\Components\Grid as InfoGrid;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;

class DirectorResource extends Resource
{
    protected static ?string $model = Director::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Reinsurers';
    protected static ?int    $navigationSort  = 2;

    public static function getNavigationBadge(): ?string
    {
        return Director::count();
    }

    /* =========================
     *  FORM  (create / edit)
     * ========================= */
    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Member Profile')
                ->compact()
                ->schema([
                    Forms\Components\Grid::make(2)->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Name')
                            ->placeholder('Please enter first name.')
                            ->required()
                            ->maxLength(200),

                        Forms\Components\TextInput::make('surname')
                            ->label('Surname')
                            ->placeholder('Please enter surname.')
                            ->required()
                            ->maxLength(200),

                        Forms\Components\ToggleButtons::make('gender')
                            ->label('Gender')
                            ->options([
                                'Male' => 'Male',
                                'Female' => 'Female',
                            ])
                            ->inline()
                            ->required(),

                        Forms\Components\TextInput::make('email')
                            ->label('Email address')
                            ->placeholder('name@example.com')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),

                        Forms\Components\TextInput::make('phone')
                            ->label('Phone')
                            ->placeholder('e.g., +52 442 123 4567 ext. 123')
                            ->tel()
                            ->required()
                            ->maxLength(40),

                        Forms\Components\Select::make('country_id')
                            ->label('Country')
                            ->relationship(
                                name: 'country',
                                titleAttribute: 'alpha_3',
                                modifyQueryUsing: fn (Builder $q) => $q->orderBy('alpha_3'),
                            )
                            ->getOptionLabelFromRecordUsing(
                                fn (Country $r) => "{$r->alpha_3} - {$r->name}"
                            )
                            ->searchable()
                            ->preload()
                            ->required()
                            ->placeholder('Select a country'),

                        Forms\Components\Textarea::make('address')
                            ->label('Address')
                            ->placeholder('Please fill address.')
                            ->required()
                            ->autosize()
                            ->columnSpan(2),

                        Forms\Components\TextInput::make('occupation')
                            ->label('Occupation')
                            ->placeholder('e.g., Chief Risk Officer')
                            ->datalist(fn () => \App\Models\Director::query()
                                ->whereNotNull('occupation')
                                ->where('occupation', '!=', '')
                                ->distinct()
                                ->orderBy('occupation')
                                ->limit(1000)
                                ->pluck('occupation')
                                ->toArray()
                            )
                            ->required()
                            ->maxLength(400)
                            ->columnSpan(2),

                        Forms\Components\Section::make('Image')
                            ->columnSpan(2)
                            ->compact()
                            ->schema([
                                Forms\Components\FileUpload::make('image')
                                    ->label('Director photo')
                                    ->disk('s3')
                                    ->directory('Directors')
                                    ->visibility('public')
                                    ->image()
                                    ->acceptedFileTypes(['image/png', 'image/jpeg', 'image/svg+xml'])
                                    ->preserveFilenames()
                                    ->previewable(false)
                                    ->downloadable()
                                    ->openable()
                                    ->helperText("Upload director’s photo (PNG, JPG, or SVG, preferably square)."),
                            ]),
                    ]),
                ]),
        ]);
    }

    /* =========================
     *  INFOLIST  (VIEW PAGE)
     * ========================= */
public static function infolist(Infolist $infolist): Infolist
{
    return $infolist->schema([
        InfoSection::make('Member Profile')->schema([
            InfoGrid::make(3)
                ->extraAttributes(['style' => 'gap: 6px;'])
                ->schema([

                // Cols 1–2: todas las filas (label + value con una sola línea por fila)
                InfoGrid::make(1)
                    ->columnSpan(2)
                    ->extraAttributes(['style' => 'row-gap: 0;'])
                    ->schema([

                        // Name
                        InfoGrid::make(2)
                            ->extraAttributes([
                                'style' => 'border-bottom:1px solid rgba(255,255,255,0.12); padding:2px 0;',
                            ])
                            ->schema([
                                TextEntry::make('name_label')
                                    ->label('')
                                    ->state('Name:')
                                    ->weight('bold')
                                    ->alignment('right')
                                    ->grow(false)
                                    ->extraAttributes(['style' => 'width:170px; margin:0;']),
                                TextEntry::make('name_value')
                                    ->label('')
                                    ->state(fn ($record) =>
                                        trim(($record->name ?? '') . ' ' . ($record->surname ?? '')) ?: '—'
                                    )
                                    ->extraAttributes(['style' => 'margin:0;']),
                            ]),

                        // Gender
                        InfoGrid::make(2)
                            ->extraAttributes([
                                'style' => 'border-bottom:1px solid rgba(255,255,255,0.12); padding:2px 0;',
                            ])
                            ->schema([
                                TextEntry::make('gender_label')
                                    ->label('')
                                    ->state('Gender:')
                                    ->weight('bold')
                                    ->alignment('right')
                                    ->grow(false)
                                    ->extraAttributes(['style' => 'width:170px; margin:0;']),
                                TextEntry::make('gender_value')
                                    ->label('')
                                    ->state(fn ($record) => $record->gender ?? '—')
                                    ->extraAttributes(['style' => 'margin:0;']),
                            ]),

                        // Email
                        InfoGrid::make(2)
                            ->extraAttributes([
                                'style' => 'border-bottom:1px solid rgba(255,255,255,0.12); padding:2px 0;',
                            ])
                            ->schema([
                                TextEntry::make('email_label')
                                    ->label('')
                                    ->state('Email address:')
                                    ->weight('bold')
                                    ->alignment('right')
                                    ->grow(false)
                                    ->extraAttributes(['style' => 'width:170px; margin:0;']),
                                TextEntry::make('email_value')
                                    ->label('')
                                    ->state(fn ($record) => $record->email ?? '—')
                                    ->extraAttributes(['style' => 'margin:0;']),
                            ]),

                        // Phone
                        InfoGrid::make(2)
                            ->extraAttributes([
                                'style' => 'border-bottom:1px solid rgba(255,255,255,0.12); padding:2px 0;',
                            ])
                            ->schema([
                                TextEntry::make('phone_label')
                                    ->label('')
                                    ->state('Phone:')
                                    ->weight('bold')
                                    ->alignment('right')
                                    ->grow(false)
                                    ->extraAttributes(['style' => 'width:170px; margin:0;']),
                                TextEntry::make('phone_value')
                                    ->label('')
                                    ->state(fn ($record) => $record->phone ?? '—')
                                    ->extraAttributes(['style' => 'margin:0;']),
                            ]),

                        // Country
                        InfoGrid::make(2)
                            ->extraAttributes([
                                'style' => 'border-bottom:1px solid rgba(255,255,255,0.12); padding:2px 0;',
                            ])
                            ->schema([
                                TextEntry::make('country_label')
                                    ->label('')
                                    ->state('Country:')
                                    ->weight('bold')
                                    ->alignment('right')
                                    ->grow(false)
                                    ->extraAttributes(['style' => 'width:170px; margin:0;']),
                                TextEntry::make('country_value')
                                    ->label('')
                                    ->state(fn ($record) =>
                                        $record->country
                                            ? "{$record->country->alpha_3} - {$record->country->name}"
                                            : '—'
                                    )
                                    ->extraAttributes(['style' => 'margin:0;']),
                            ]),

                        // Address (multi-línea; deja el mismo padding compacto)
                        InfoGrid::make(2)
                            ->extraAttributes([
                                'style' => 'border-bottom:1px solid rgba(255,255,255,0.12); padding:2px 0;',
                            ])
                            ->schema([
                                TextEntry::make('address_label')
                                    ->label('')
                                    ->state('Address:')
                                    ->weight('bold')
                                    ->alignment('right')
                                    ->grow(false)
                                    ->extraAttributes(['style' => 'width:170px; margin:0;']),
                                TextEntry::make('address_value')
                                    ->label('')
                                    ->state(fn ($record) => $record->address ?? '—')
                                    ->extraAttributes(['style' => 'margin:0; line-height:1.2;']),
                            ]),

                        // Occupation
                        InfoGrid::make(2)
                            ->extraAttributes([
                                'style' => 'border-bottom:1px solid rgba(255,255,255,0.12); padding:2px 0;',
                            ])
                            ->schema([
                                TextEntry::make('occupation_label')
                                    ->label('')
                                    ->state('Occupation:')
                                    ->weight('bold')
                                    ->alignment('right')
                                    ->grow(false)
                                    ->extraAttributes(['style' => 'width:170px; margin:0;']),
                                TextEntry::make('occupation_value')
                                    ->label('')
                                    ->state(fn ($record) => $record->occupation ?? '—')
                                    ->extraAttributes(['style' => 'margin:0;']),
                            ]),
                    ]),

                // Col 3: foto
                ImageEntry::make('image')
                    ->label('')
                    ->columnSpan(1)
                    ->height(190)
                    ->circular(),
            ]),
        ])
        ->maxWidth('4xl')
        ->collapsible(),
    ]);
}







    /* =========================
     *  TABLE
     * ========================= */
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('Index')
                    ->label('Index')
                    ->state(fn ($record, $rowLoop) => $rowLoop->iteration),

                TextColumn::make('person')
                    ->label('Director')
                    ->state(fn (Director $r) =>
                        trim(($r->name ?? '') . ' ' . ($r->surname ?? '')) ?: '—'
                    )
                    ->searchable(query: function (Builder $q, string $search) {
                        $q->where(fn ($w) =>
                            $w->where('name', 'like', "%{$search}%")
                              ->orWhere('surname', 'like', "%{$search}%")
                        );
                    })
                    ->sortable(query: function (Builder $q, string $dir) {
                        $q->orderBy('name', $dir)->orderBy('surname', $dir);
                    }),

                TextColumn::make('gender')->searchable(),
                TextColumn::make('email')
                    ->label('Email')
                    ->formatStateUsing(fn ($state) => $state ?: '—')
                    ->color(fn ($state) => blank($state) ? 'gray' : null)
                    ->copyable()
                    ->searchable(),

                TextColumn::make('occupation')->searchable(),
                TextColumn::make('country.alpha_3')->label('Country')->sortable()->searchable(),
                TextColumn::make('created_at')->dateTime()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')->dateTime()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('deleted_at')->dateTime()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('name', 'asc')
            ->actions([
                Tables\Actions\ActionGroup::make([
                    // 👉 Hacemos que View NAVEGUE a la página View (que usa el infolist):
                    Tables\Actions\ViewAction::make()
                        ->url(fn ($record) => static::getUrl('view', ['record' => $record]))
                        ->openUrlInNewTab(false),

                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    /* =========================
     *  PAGES (incluye VIEW)
     * ========================= */
    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListDirectors::route('/'),
            'create' => Pages\CreateDirector::route('/create'),
            'view'   => Pages\ViewDirector::route('/{record}'), // 👈 importante
            'edit'   => Pages\EditDirector::route('/{record}/edit'),
        ];
    }
}
