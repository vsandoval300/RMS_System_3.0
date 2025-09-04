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


class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
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
                        ->maxLength(255),

                    TextInput::make('email')
                        ->label('Email')
                        ->placeholder('name@example.com')
                        ->email()
                        ->required()
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
                        ->afterStateHydrated(fn ($component, $state) => $component->state('')) // Oculta valor actual
                        ->dehydrateStateUsing(fn ($state) => !empty($state) ? Hash::make($state) : null),

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

    public static function table(Table $table): Table
    {
        return $table
        ->modifyQueryUsing(fn (Builder $query) => $query->with('department'))
        ->columns([
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
