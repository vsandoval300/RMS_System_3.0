<?php

namespace App\Filament\Resources\Clients\RelationManagers;

use Filament\Schemas\Schema;
use Filament\Actions\AttachAction;
use Filament\Actions\DetachAction;
use Filament\Actions\DetachBulkAction;
use Filament\Forms;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\TextColumn;

class IndustriesRelationManager extends RelationManager
{
    protected static string $relationship = 'industries';
    protected static ?string $recordTitleAttribute = 'name';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('short_name')
                        ->label('Short Name')
                        ->required()
                        ->unique()
                        ->live(onBlur: false)
                        ->maxLength(255)
                        ->afterStateUpdated(fn ($state, callable $set) => $set('short_name', ucwords(strtolower($state))))
                        ->helperText('First letter of each word will be capitalised.'),
                       
                    
                    Textarea::make('description')
                        ->label('Description')
                        ->required()
                        ->columnSpan('full')
                        ->autosize()
                        ->afterStateUpdated(fn ($state, callable $set) => $set('description', ucfirst(strtolower($state))))
                        ->helperText('Please provide a brief description of the sector. Only the first letter will be capitalised.'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Industry')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('description')
                    ->wrap()
                    ->extraAttributes([
                        'style' => 'max-width: 950px; white-space: normal;',
                    ]),
            ])

            // ───── Header actions ─────
            ->headerActions([
                AttachAction::make()
                    ->label('Add industry')
                    ->modalHeading('Attach Industry')
                    ->preloadRecordSelect()
                    ->recordSelectSearchColumns(['name', 'description']),
            ])

            // ───── Row actions ─────
            ->recordActions([
                DetachAction::make()
                    ->label('Delete')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Delete industry')
                    ->modalDescription('Are you sure you want to delete this industry from the client?'),
            ])

            // ───── Bulk actions ─────
            ->toolbarActions([
                DetachBulkAction::make()
                    ->label('Delete selected')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Delete selected industries')
                    ->modalDescription('Are you sure you want to delete the selected industries from the client?'),
            ]);
    }
}