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
                ->schema([
                    TextInput::make('name')
                        ->label('Name')
                        ->required()
                        ->maxLength(255),

                    TextInput::make('email')
                        ->label('Email')
                        ->email()
                        ->required()
                        ->maxLength(255)
                        ->unique(ignoreRecord: true),

                    TextInput::make('password')
                        ->label('Password')
                        ->password()
                        ->dehydrated(fn ($state) => filled($state)) // Solo guarda si hay input
                        ->maxLength(255)
                        ->required(fn (string $context): bool => $context === 'create')
                        ->afterStateHydrated(fn ($component, $state) => $component->state('')) // Oculta valor actual
                        ->dehydrateStateUsing(fn ($state) => !empty($state) ? Hash::make($state) : null),

                    DateTimePicker::make('email_verified_at')
                        ->label('Email Verified At')
                        ->nullable(),

                    Select::make('roles')
                        ->label('Roles')
                        ->multiple()
                        ->options(Role::pluck('name', 'name'))
                        ->default(fn ($record) => $record?->getRoleNames())
                        ->dehydrateStateUsing(fn ($state) => $state)
                        ->afterStateUpdated(fn ($state, $record) => $record->syncRoles($state))
                        ->searchable()
                        ->preload(),
                ])
                ->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
        ->columns([
            TextColumn::make('name')
                ->label('Name')
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
                    ->collapsible(), // 👈 clave para colapsar
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
            Tables\Actions\DeleteBulkAction::make(),
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
