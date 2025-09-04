<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DirectorResource\Pages;
use App\Filament\Resources\DirectorResource\RelationManagers;
use App\Models\Director;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Section;
use Illuminate\Support\Facades\Storage;
use App\Models\Country;

class DirectorResource extends Resource
{
    protected static ?string $model = Director::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Reinsurers';
    protected static ?int    $navigationSort  = 2;   // aparecerá primero

    /* ───── NUEVO: burbuja con el total en el menú ───── */
    public static function getNavigationBadge(): ?string
    {
        // Puedes usar self::$model::count() o Reinsurer::count()
        return Director::count();
    }

   public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Member Profile')
                ->compact()
                ->schema([
                    Forms\Components\Grid::make(2)->schema([
                        // ── Identidad ───────────────────────────────
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
                            ->required()
                            ->rule('in:M,F'),

                        // ── Contacto ────────────────────────────────
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

                        // ── Ubicación ───────────────────────────────
                        Forms\Components\Select::make('country_id')
                            ->label('Country')
                            ->relationship(
                                name: 'country',
                                titleAttribute: 'alpha_3',
                                modifyQueryUsing: fn (Builder $query) => $query->orderBy('alpha_3'),
                            )
                            ->getOptionLabelFromRecordUsing(
                                fn (Country $record) => "{$record->alpha_3} - {$record->name}"
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
                            ->columnSpan(2), // ocupa toda la fila

                        // ── Rol ─────────────────────────────────────
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

                        // ── Foto ────────────────────────────────────
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
                                    ->hint(fn ($record) =>
                                        $record?->icon
                                            ? 'Existing photo: ' . basename($record->icon)
                                            : 'No photo uploaded yet.'
                                    )
                                    ->helperText("Upload director’s photo (PNG, JPG, or SVG, preferably square)."),
                            ]),
                    ]),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('Index')
                    ->label('Index')
                    ->state(fn ($record, $rowLoop) => $rowLoop->iteration)
                    ->sortable(false) // 👈 no tiene sentido ordenar este índice
                    ->searchable(false), // 👈 tampoco buscarlo

                TextColumn::make('person')
                    ->label('Director')
                    // ← define el valor de la columna virtual
                    ->state(fn (Director $r) => trim(($r->name ?? '') . ' ' . ($r->surname ?? '')) ?: '—')

                    // búsqueda por nombre y apellido
                    ->searchable(query: function (Builder $q, string $search) {
                        $q->where(fn ($w) =>
                            $w->where('name', 'like', "%{$search}%")
                            ->orWhere('surname', 'like', "%{$search}%")
                        );
                    })

                    // orden por nombre y luego apellido
                    ->sortable(query: function (Builder $q, string $dir) {
                        $q->orderBy('name', $dir)->orderBy('surname', $dir);
                    }),
                                TextColumn::make('gender')
                                    ->searchable(),
                                TextColumn::make('email')
                    ->label('Email')
                    ->formatStateUsing(fn ($state) => $state ?: '—')   // muestra guion si es null/vacío
                    ->color(fn ($state) => blank($state) ? 'gray' : null)
                    ->copyable()                                       // opcional: botón para copiar
                    ->searchable(),    
                /* TextColumn::make('phone')
                    ->searchable(),
                TextColumn::make('address')
                    ->searchable(), */
                TextColumn::make('occupation')
                    ->searchable(),
                /* ImageColumn::make('image'), */
                TextColumn::make('country.alpha_3')
                    ->label('Country')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('name', 'asc') 

            ->filters([
                //
            ])
            ->actions([
                 Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make()
                    ->label('View')
                    ->url(fn (Director $record) =>
                        self::getUrl('view', ['record' => $record])
                    )
                    ->icon('heroicon-m-eye'),  // opcional

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
            'index' => Pages\ListDirectors::route('/'),
            'create' => Pages\CreateDirector::route('/create'),
            'view'   => Pages\ViewDirector::route('/{record}'), // 👈 agregar
            'edit' => Pages\EditDirector::route('/{record}/edit'),
        ];
    }
}
