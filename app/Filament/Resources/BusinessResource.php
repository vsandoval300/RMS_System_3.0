<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BusinessResource\Pages;
use App\Filament\Resources\BusinessResource\RelationManagers;
use App\Models\Business;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BusinessResource extends Resource
{
    protected static ?string $model = Business::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Underwritten';
    protected static ?int    $navigationSort  = 8;   // aparecerá primero

     /* ───── NUEVO: burbuja con el total en el menú ───── */
    public static function getNavigationBadge(): ?string
    {
        return Business::count();
    } 

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('index')
                    ->required()
                    ->numeric(),
                Forms\Components\Textarea::make('description')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('reinsurance_type')
                    ->required()
                    ->maxLength(510),
                Forms\Components\TextInput::make('risk_covered')
                    ->required()
                    ->maxLength(510),
                Forms\Components\TextInput::make('business_type')
                    ->required()
                    ->maxLength(510),
                Forms\Components\TextInput::make('premium_type')
                    ->required()
                    ->maxLength(510),
                Forms\Components\TextInput::make('purpose')
                    ->required()
                    ->maxLength(510),
                Forms\Components\TextInput::make('claims_type')
                    ->required()
                    ->maxLength(510),
                Forms\Components\TextInput::make('reinsurer_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('parent_id')
                    ->maxLength(38),
                Forms\Components\TextInput::make('renewed_from_id')
                    ->maxLength(38),
                Forms\Components\TextInput::make('producer_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('currency_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('region_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('approval_status')
                    ->required()
                    ->maxLength(510)
                    ->default('DFT'),
                Forms\Components\DateTimePicker::make('approval_status_updated_at'),
                Forms\Components\TextInput::make('business_lifecycle_status')
                    ->required()
                    ->maxLength(510)
                    ->default('On Hold'),
                Forms\Components\DateTimePicker::make('business_lifecycle_status_updated_at'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('index')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('business_code')
                    ->searchable(),
                Tables\Columns\TextColumn::make('reinsurance_type')
                    ->searchable(),
                Tables\Columns\TextColumn::make('risk_covered')
                    ->searchable(),
                Tables\Columns\TextColumn::make('business_type')
                    ->searchable(),
                Tables\Columns\TextColumn::make('premium_type')
                    ->searchable(),
                Tables\Columns\TextColumn::make('purpose')
                    ->searchable(),
                Tables\Columns\TextColumn::make('claims_type')
                    ->searchable(),
                Tables\Columns\TextColumn::make('reinsurer_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('parent_id')
                    ->searchable(),
                Tables\Columns\TextColumn::make('renewed_from_id')
                    ->searchable(),
                Tables\Columns\TextColumn::make('producer_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('currency_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('region_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('approval_status')
                    ->searchable(),
                Tables\Columns\TextColumn::make('approval_status_updated_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('business_lifecycle_status')
                    ->searchable(),
                Tables\Columns\TextColumn::make('business_lifecycle_status_updated_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListBusinesses::route('/'),
            'create' => Pages\CreateBusiness::route('/create'),
            'edit' => Pages\EditBusiness::route('/{record}/edit'),
        ];
    }
}
