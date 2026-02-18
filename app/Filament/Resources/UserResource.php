<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Illuminate\Support\Facades\Hash;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Grouping\Group;
use Filament\Support\RawJs;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Grid as InfoGrid;
use Filament\Infolists\Components\Section as InfoSection;
use Filament\Infolists\Components\ImageEntry;
use Illuminate\Database\Eloquent\Model;


class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationGroup = 'Security';
    protected static ?int    $navigationSort  = -110;   // aparecerá primero

    /* ───── NUEVO: burbuja con el total en el menú ───── */
    public static function getNavigationBadge(): ?string
    {
        // Puedes usar self::$model::count() o Reinsurer::count()
        return User::count();
    }

    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            Section::make('User Information')
                 ->description("Overview of the user's primary details.")
                ->schema([
                    TextInput::make('name')
                        ->label('Name')
                        ->placeholder('Please provide user name')
                        //->inlineLabel()
                        ->required()
                        ->unique(ignorable: fn (?Model $record) => $record)
                        ->maxLength(255),

                    TextInput::make('email')
                        ->label('Email')
                        ->placeholder('name@example.com')
                        ->email()
                        ->required()
                        ->unique(ignorable: fn (?Model $record) => $record)
                        ->maxLength(255)
                        ->rule('regex:/^[\w\.-]+@[\w\.-]+\.\w+$/')
                        ->live(onBlur: true)
                        ->unique(ignoreRecord: true),
                        
                    TextInput::make('password')
                        ->label('Password')
                        ->visible(fn (string $context) => in_array($context, ['create','edit'], true))
                        ->placeholder('Please provide password')
                        ->password()
                        ->dehydrated(fn ($state) => filled($state)) // Solo guarda si hay input
                        ->maxLength(255)
                        ->required(fn (string $context): bool => $context === 'create')
                        ->afterStateHydrated(fn ($component, $state) => $component->state('')), // Oculta valor actual
                        //->dehydrateStateUsing(fn ($state) => !empty($state) ? Hash::make($state) : null),

                    Select::make('department_id')
                        ->label('Department')
                        //->inlineLabel()
                        ->placeholder('Select department')
                        ->relationship('department', 'name')
                        ->searchable()
                        ->preload()
                        ->required()
                        ->native(false),         

                    Select::make('position_id')
                        ->label('Position')
                        ->placeholder('Select position')
                        //->inlineLabel()
                        ->relationship('position', 'position')
                        ->searchable()
                        ->preload()
                        ->required()
                        ->native(false),
                                
                ])
                ->columns(2),
            
            Section::make('Audit Dates')
                ->visible(fn (string $context) => $context === 'view') 
                ->schema([
                  
                    DateTimePicker::make('email_verified_at')
                        ->label('Email Verified At')
                        //->inlineLabel()
                        ->disabled() 
                        ->dehydrated(false)
                        ->visible(fn (string $context) => in_array($context, ['edit', 'view'], true))
                        ->nullable(),

                    DateTimePicker::make('created_at')
                        ->label('Created At')
                        //->inlineLabel()
                        ->disabled() 
                        ->dehydrated(false)
                        ->visible(fn (string $context) => in_array($context, ['edit', 'view'], true))
                        ->nullable(),

                    DateTimePicker::make('updated_at')
                        ->label('Updated At')
                        //->inlineLabel()
                        ->disabled() 
                        ->dehydrated(false)
                        ->visible(fn (string $context) => in_array($context, ['edit', 'view'], true))
                        ->nullable(),

                ])               
                ->columns(3),

            Section::make('Roles')
                ->description('Grant or revoke roles to adjust access level.')
                ->schema([
                    Select::make('roles')
                            ->label('Roles')
                            ->multiple()
                            ->relationship(
                                name: 'roles',
                                titleAttribute: 'name',
                                modifyQueryUsing: fn (Builder $query) => $query->where('guard_name', 'web')
                            )
                            ->preload()
                            ->searchable(),

            ])
        ]);
    }




public static function infolist(Infolist $infolist): Infolist
{
    return $infolist->schema([

        /* ─────────────────────────  PROFILE  ───────────────────────── */
        InfoSection::make('User Profile')->schema([
            InfoGrid::make(3)
                ->extraAttributes(['style' => 'gap: 6px;'])
                ->schema([

                    // Cols 1–2: filas compactas "Label + Value"
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
                                        ->state(fn ($record) => ($record->name ?: '—'))
                                        ->columnSpan(9),
                                ]),

                            // Email
                            InfoGrid::make(12)
                                ->extraAttributes(['style' => 'border-bottom:1px solid rgba(255,255,255,0.12); padding:2px 0;'])
                                ->schema([
                                    TextEntry::make('email_label')
                                        ->label('')
                                        ->state('Email:')
                                        ->weight('bold')
                                        ->alignment('right')
                                        ->columnSpan(3),
                                    TextEntry::make('email_value')
                                        ->label('')
                                        ->state(fn ($record) => $record->email ?? '—')
                                        ->columnSpan(9),
                                ]),

                            // Department
                            InfoGrid::make(12)
                                ->extraAttributes(['style' => 'border-bottom:1px solid rgba(255,255,255,0.12); padding:2px 0;'])
                                ->schema([
                                    TextEntry::make('department_label')
                                        ->label('')
                                        ->state('Department:')
                                        ->weight('bold')
                                        ->alignment('right')
                                        ->columnSpan(3),
                                    TextEntry::make('department_value')
                                        ->label('')
                                        ->state(fn ($record) => $record->department?->name ?: '—')
                                        ->columnSpan(9),
                                ]),

                            // Position
                            InfoGrid::make(12)
                                ->extraAttributes(['style' => 'border-bottom:1px solid rgba(255,255,255,0.12); padding:2px 0;'])
                                ->schema([
                                    TextEntry::make('position_label')
                                        ->label('')
                                        ->state('Position:')
                                        ->weight('bold')
                                        ->alignment('right')
                                        ->columnSpan(3),
                                    TextEntry::make('position_value')
                                        ->label('')
                                        ->state(fn ($record) => $record->position?->position ?: '—')
                                        ->columnSpan(9),
                                ]),

                            // Roles (chips)
                            InfoGrid::make(12)
                                ->extraAttributes(['style' => 'border-bottom:1px solid rgba(255,255,255,0.12); padding:2px 0;'])
                                ->schema([
                                    TextEntry::make('roles_label')
                                        ->label('')
                                        ->state('Roles:')
                                        ->weight('bold')
                                        ->alignment('right')
                                        ->columnSpan(3),
                                    TextEntry::make('roles_value')
                                        ->label('')
                                        ->html()
                                        ->state(function ($record) {
                                            $names = $record->roles?->pluck('name')->all() ?? [];
                                            if (empty($names)) return '—';
                                            $chips = array_map(
                                                fn ($n) => "<span style='display:inline-block;padding:2px 8px;border-radius:9999px;background:rgba(255,255,255,0.08);font-size:12px;margin-right:6px;'>{$n}</span>",
                                                $names
                                            );
                                            return implode('', $chips);
                                        })
                                        ->columnSpan(9),
                                ]),
                        ]),

                    // Col 3: avatar por iniciales
                   InfoGrid::make(1)
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
                                // ✅ usar state() en vez de getStateUsing()
                                ->state(fn ($record) => data_get($record, 'image'))
                                ->hidden(fn ($record) => blank(data_get($record, 'image')))
                                ->extraAttributes([
                                    'style' => '
                                        min-height:230px; width:100%;
                                        border-radius:14px;
                                        background:linear-gradient(135deg, rgba(255,255,255,0.08), rgba(255,255,255,0.03));
                                        border:1px solid rgba(255,255,255,0.15);
                                        display:flex; align-items:center; justify-content:center;
                                        padding:6px; margin:0; overflow:hidden;
                                    ',
                                ])
                                ->extraImgAttributes([
                                    'style' => 'width:96%;height:96%;object-fit:contain;display:block;',
                                ]),

                            TextEntry::make('user_image_placeholder')
                                ->label('')->html()
                                ->state('
                                    <div style="
                                        min-height:230px; width:100%;
                                        border-radius:14px;
                                        display:flex; align-items:center; justify-content:center;
                                        margin:0;
                                        border:1px dashed rgba(255,255,255,0.25);
                                        background:linear-gradient(135deg, rgba(255,255,255,0.04), rgba(255,255,255,0.02));
                                    "></div>
                                ')
                                ->visible(fn ($record) => blank(data_get($record, 'image')))
                                ->extraAttributes(['style' => 'margin:0; padding:0;']),
                        ]),
                ]),
        ])
        ->maxWidth('4xl')
        ->collapsible(),
        /* ─────────────────────────  AUDIT  ───────────────────────── */
        InfoSection::make('Audit Dates')
            ->schema([
                InfoGrid::make(2)
                    ->extraAttributes(['style' => 'gap: 12px;'])
                    ->schema([
                        // Email verified
                        InfoGrid::make(2)
                            ->extraAttributes(['style' => 'border-bottom:1px solid rgba(255,255,255,0.12); padding:2px 0;'])
                            ->schema([
                                TextEntry::make('ev_label')
                                    ->label('')->state('Email Verified At:')->weight('bold')
                                    ->alignment('right')->grow(false)
                                    ->extraAttributes(['style' => 'width:170px; margin:0;']),
                                TextEntry::make('email_verified_at')
                                    ->label('')
                                    ->state(fn ($record) => $record->email_verified_at?->format('Y-m-d H:i') ?: '—')
                                    ->extraAttributes(['style' => 'margin:0;']),
                            ]),

                        // Created at
                        InfoGrid::make(2)
                            ->extraAttributes(['style' => 'border-bottom:1px solid rgba(255,255,255,0.12); padding:2px 0;'])
                            ->schema([
                                TextEntry::make('created_label')
                                    ->label('')->state('Created At:')->weight('bold')
                                    ->alignment('right')->grow(false)
                                    ->extraAttributes(['style' => 'width:170px; margin:0;']),
                                TextEntry::make('created_value')
                                    ->label('')
                                    ->state(fn ($record) => $record->created_at?->format('Y-m-d H:i') ?: '—')
                                    ->extraAttributes(['style' => 'margin:0;']),
                            ]),

                        // Updated at
                        InfoGrid::make(2)
                            ->extraAttributes(['style' => 'border-bottom:1px solid rgba(255,255,255,0.12); padding:2px 0;'])
                            ->schema([
                                TextEntry::make('updated_label')
                                    ->label('')->state('Updated At:')->weight('bold')
                                    ->alignment('right')->grow(false)
                                    ->extraAttributes(['style' => 'width:170px; margin:0;']),
                                TextEntry::make('updated_value')
                                    ->label('')
                                    ->state(fn ($record) => $record->updated_at?->format('Y-m-d H:i') ?: '—')
                                    ->extraAttributes(['style' => 'margin:0;']),
                            ]),
                    ]),
            ])
            ->maxWidth('4xl')
            ->compact(),
    ]);
}















    public static function table(Table $table): Table
    {
        return $table
        ->modifyQueryUsing(fn (Builder $query) => $query->with('department'))
        ->columns([

            TextColumn::make('id')
                ->label('Id')
                ->sortable()
                ->searchable()
                ->copyable()
                //->toggleable(isToggledHiddenByDefault: true)
                ->extraAttributes(['class' => 'w-24 text-gray-500']),

            TextColumn::make('name')
                ->label('Name')
                ->formatStateUsing(function (string $state): string {
                    $name = trim($state);
                    $parts = preg_split('/\s+/', $name) ?: [];
                    $first = mb_substr($parts[0] ?? '', 0, 1);
                    $last  = mb_substr(($parts[count($parts) - 1] ?? ''), 0, 1);
                    $initials = mb_strtoupper($first . ($last !== $first ? $last : ''));
                    $escName = e($name);

                    $circleBg = '#41a2c3'; // ← tu color

                    return <<<HTML
                        <span class="flex items-center gap-2">
                            <span class="inline-flex items-center justify-center h-6 w-6 rounded-full text-white text-[10px] font-semibold"
                                style="background: {$circleBg}">
                                {$initials}
                            </span>
                            <span>{$escName}</span>
                        </span>
                    HTML;
                })
                ->html()
                ->searchable()
                ->sortable(),


            TextColumn::make('email')
                ->icon('heroicon-m-envelope')
                ->label('Email')
                ->searchable()
                ->sortable(),

            TextColumn::make('position.position')
                ->label('Position')
                ->placeholder('-')     // por si viene null
                ->searchable()
                ->sortable(),

            TextColumn::make('department.name')
                ->label('Department')
                ->placeholder('-')     // por si viene null
                ->searchable()
                ->sortable(),

            TextColumn::make('roles.name')
                ->label('Roles')
                ->badge()
                ->color('primary')
                ->separator(', ')
                ->sortable()
                ->searchable(),

        ])
        ->defaultSort('department.name','asc')
        ->groups([
            Group::make('department.name')
                ->label('Department')
                ->getDescriptionFromRecordUsing(
                    fn (\App\Models\User $record): string =>
                        Str::limit((string) ($record->department?->description ?? ''), 140) // ajusta 120–160
                )
                ->collapsible(),
        ])
        ->defaultGroup('department.name') // 👈 activa el grupo automáticamente









        ->filters([
            // Puedes agregar filtros por rol aquí si lo deseas
        ])
        ->actions([

            Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ])
            
        ])
        ->bulkActions([
            //Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
