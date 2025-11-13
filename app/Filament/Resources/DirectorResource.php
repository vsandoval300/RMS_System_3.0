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
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\ToggleButtons;
use Illuminate\Support\Str;


// 👇 IMPORTS para INFOLIST
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section as InfoSection;
use Filament\Infolists\Components\Grid as InfoGrid;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;

class DirectorResource extends Resource
{
    protected static ?string $model = Director::class;

    protected static ?string $navigationIcon = 'heroicon-o-minus';
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
                        TextInput::make('name')
                            ->label('Name')
                            ->placeholder('Please enter first name.')
                            ->required()
                            ->maxLength(200),

                        TextInput::make('surname')
                            ->label('Surname')
                            ->placeholder('Please enter surname.')
                            ->required()
                            ->maxLength(200),

                        ToggleButtons::make('gender')
                            ->label('Gender')
                            ->options([
                                'Male' => 'Male',
                                'Female' => 'Female',
                            ])
                            ->inline()
                            ->required(),

                        TextInput::make('email')
                            ->label('Email address')
                            ->placeholder('name@example.com')
                            ->email()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),

                        TextInput::make('phone')
                            ->label('Phone')
                            ->placeholder('e.g., +52 442 123 4567 ext. 123')
                            ->rules(['nullable', 'string', 'max:40']),
                            

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
                            ->placeholder('Select a country'),
                            //->helperText('Choose the reinsurer\'s country.'),

                        Textarea::make('address')
                            ->label('Address')
                            ->placeholder('Please fill address.')
                            ->required()
                            ->autosize()
                            ->columnSpan(2),

                        TextInput::make('occupation')
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

                        Section::make('Image')
                            ->columnSpan(2)
                            ->compact()
                            ->schema([
                                FileUpload::make('image')
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
                                        ->state(fn ($record) =>
                                            trim(($record->name ?? '') . ' ' . ($record->surname ?? '')) ?: '—'
                                        )
                                        ->columnSpan(9),
                                ]),

                            // Gender
                            InfoGrid::make(12)
                                ->extraAttributes(['style' => 'border-bottom:1px solid rgba(255,255,255,0.12); padding:2px 0;'])
                                ->schema([
                                    TextEntry::make('gender_label')
                                        ->label('')
                                        ->state('Gender:')
                                        ->weight('bold')
                                        ->alignment('right')
                                        ->columnSpan(3),
                                    TextEntry::make('gender_value')
                                        ->label('')
                                        ->state(fn ($record) => $record->gender ?? '—')
                                        ->columnSpan(9),
                                ]),

                            // Email
                            InfoGrid::make(12)
                                ->extraAttributes(['style' => 'border-bottom:1px solid rgba(255,255,255,0.12); padding:2px 0;'])
                                ->schema([
                                    TextEntry::make('email_label')
                                        ->label('')
                                        ->state('Email address:')
                                        ->weight('bold')
                                        ->alignment('right')
                                        ->columnSpan(3),
                                    TextEntry::make('email_value')
                                        ->label('')
                                        ->state(fn ($record) => $record->email ?? '—')
                                        ->columnSpan(9),
                                ]),

                            // Phone
                            InfoGrid::make(12)
                                ->extraAttributes(['style' => 'border-bottom:1px solid rgba(255,255,255,0.12); padding:2px 0;'])
                                ->schema([
                                    TextEntry::make('phone_label')
                                        ->label('')
                                        ->state('Phone:')
                                        ->weight('bold')
                                        ->alignment('right')
                                        ->columnSpan(3),
                                    TextEntry::make('phone_value')
                                        ->label('')
                                        ->state(fn ($record) => $record->phone ?? '—')
                                        ->columnSpan(9),
                                ]),

                            // Country
                            InfoGrid::make(12)
                                ->extraAttributes(['style' => 'border-bottom:1px solid rgba(255,255,255,0.12); padding:2px 0;'])
                                ->schema([
                                    TextEntry::make('country_label')
                                        ->label('')
                                        ->state('Country:')
                                        ->weight('bold')
                                        ->alignment('right')
                                        ->columnSpan(3),
                                    TextEntry::make('country_value')
                                        ->label('')
                                        ->state(fn ($record) =>
                                            $record->country
                                                ? "{$record->country->alpha_3} - {$record->country->name}"
                                                : '—'
                                        )
                                        ->columnSpan(9),
                                ]),

                            // Address (multi-línea)
                            InfoGrid::make(12)
                                ->extraAttributes(['style' => 'border-bottom:1px solid rgba(255,255,255,0.12); padding:2px 0;'])
                                ->schema([
                                    TextEntry::make('address_label')
                                        ->label('')
                                        ->state('Address:')
                                        ->weight('bold')
                                        ->alignment('right')
                                        ->columnSpan(3),
                                    TextEntry::make('address_value')
                                        ->label('')
                                        ->state(fn ($record) => $record->address ?? '—')
                                        ->extraAttributes(['style' => 'line-height:1.2;'])
                                        ->columnSpan(9),
                                ]),

                            // Occupation
                            InfoGrid::make(12)
                                ->extraAttributes(['style' => 'border-bottom:1px solid rgba(255,255,255,0.12); padding:2px 0;'])
                                ->schema([
                                    TextEntry::make('occupation_label')
                                        ->label('')
                                        ->state('Occupation:')
                                        ->weight('bold')
                                        ->alignment('right')
                                        ->columnSpan(3),
                                    TextEntry::make('occupation_value')
                                        ->label('')
                                        ->state(fn ($record) => $record->occupation ?? '—')
                                        ->columnSpan(9),
                                ]),
                        ]),

                    // Col 3: foto
                   
                            /* InfoGrid::make(1)
                                ->columnSpan(1)
                                ->extraAttributes(['style' => 'display:flex;flex-direction:column;gap:6px;height:100%;'])
                                ->schema([
                                    TextEntry::make('photo_title')
                                        ->label('')->state('Photo')->weight('bold')
                                        ->extraAttributes(['style' => 'margin:0 0 4px 2px;']),

                                    ImageEntry::make('user_image')
                                        ->label('')
                                        ->disk('s3')
                                        ->visibility('public')
                                        ->getStateUsing(fn ($record) => data_get($record, 'image'))
                                        ->hidden(fn ($record) => blank(data_get($record, 'image')))
                                        ->extraAttributes([
                                            'style' => '
                                                min-height:360px; width:100%;
                                                border-radius:14px;
                                                background:linear-gradient(135deg, rgba(255,255,255,0.08), rgba(255,255,255,0.03));
                                                border:1px solid rgba(255,255,255,0.15);
                                                display:flex; align-items:center; justify-content:center;
                                                padding:6px; margin:0; overflow:hidden;
                                            ',
                                        ])
                                        ->extraImgAttributes([
                                            // ocupa más área sin recortar
                                            'style' => 'width:100%;height:100%;object-fit:contain;display:block;',
                                        ]),

                                    TextEntry::make('user_image_placeholder')
                                        ->label('')
                                        ->html()
                                        ->state('
                                            <div style="
                                                min-height:360px; width:100%;
                                                border-radius:14px;
                                                display:flex; align-items:center; justify-content:center;
                                                margin:0;
                                                border:1px dashed rgba(255,255,255,0.25);
                                                background:linear-gradient(135deg, rgba(255,255,255,0.04), rgba(255,255,255,0.02));
                                            "></div>
                                        ')
                                        ->visible(fn ($record) => blank(data_get($record, 'image')))
                                        ->extraAttributes(['style' => 'margin:0; padding:0;']),
                                ]), */

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
            ->recordUrl(fn (Director $record) => static::getUrl('view', ['record' => $record]))
            ->columns([

                TextColumn::make('Index')
                    ->label('Index')
                    ->state(fn ($record, $rowLoop) => $rowLoop->iteration),

                TextColumn::make('person')
                    ->label('Director')
                    ->html() // vamos a devolver HTML
                    // Estado “base”: nombre completo, útil para exportar / buscar
                    ->state(fn (Director $r) =>
                        trim(($r->name ?? '') . ' ' . ($r->surname ?? '')) ?: '—'
                    )
                    ->formatStateUsing(function ($state, Director $record) {
                        $name = e($state ?: '—');      // por seguridad, escapamos el texto
                        $imagePath = $record->image;   // ruta tipo "Directors/CFH.jpeg"

                        // Si no hay imagen, solo mostramos el nombre
                        if (blank($imagePath)) {
                            return "<span>{$name}</span>";
                        }

                        // Construimos la URL pública del S3
                        $imageUrl = Str::startsWith($imagePath, ['http://', 'https://'])
                            ? $imagePath
                            : rtrim(config('filesystems.disks.s3.url'), '/') . '/' . ltrim($imagePath, '/');

                        return "
                            <div style='display:flex;align-items:center;gap:8px;'>
                                <img src=\"{$imageUrl}\"
                                    alt=\"{$name}\"
                                    style='width:24px;height:24px;border-radius:50%;object-fit:cover;' />
                                <span>{$name}</span>
                            </div>
                        ";
                    })
                    ->searchable(query: function (Builder $q, string $search) {
                        $q->where(fn ($w) =>
                            $w->where('name', 'like', "%{$search}%")
                            ->orWhere('surname', 'like', "%{$search}%")
                        );
                    })
                    ->sortable(query: function (Builder $q, string $dir) {
                        $q->orderBy('name', $dir)->orderBy('surname', $dir);
                    }),

               /*  TextColumn::make('person')
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
                    }), */

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
                    Tables\Actions\ViewAction::make(),
                        //->url(fn ($record) => static::getUrl('view', ['record' => $record]))
                        //->openUrlInNewTab(false),

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
