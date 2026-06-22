<?php

namespace App\Filament\Resources\Transactions\RelationManagers;

use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Resources\RelationManagers\RelationManager;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Storage;

class RecalculationsRelationManager extends RelationManager
{
    protected static string $relationship = 'recalculations';
    protected static ?string $title = 'Bordereaux Adjustments';

    public function canCreate(): bool
    {
        return false;
    }

    public function canEdit($record): bool
    {
        return false;
    }

    public function canDelete($record): bool
    {
        return false;
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('bordereaux_reference')
            ->defaultSort('recalculation_no')
            ->columns([
                TextColumn::make('recalculation_no')
                    ->label('#')
                    ->alignCenter()
                    ->width('50px'),

                TextColumn::make('bordereaux_reference')
                    ->label('Bordereaux Reference')
                    ->searchable(),

                TextColumn::make('reported_premium')
                    ->label('Reported Premium')
                    ->numeric(decimalPlaces: 2)
                    ->alignEnd(),

                TextColumn::make('reported_claims')
                    ->label('Reported Claims')
                    ->numeric(decimalPlaces: 2)
                    ->alignEnd(),

                TextColumn::make('previous_amount')
                    ->label('Previous Amount')
                    ->numeric(decimalPlaces: 2)
                    ->alignEnd(),

                TextColumn::make('new_amount')
                    ->label('New Amount')
                    ->numeric(decimalPlaces: 2)
                    ->alignEnd()
                    ->color('success'),

                TextColumn::make('reported_premium')
                    ->label('Reported Premium')
                    ->numeric(decimalPlaces: 2)
                    ->alignEnd()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('reported_claims')
                    ->label('Reported Claims')
                    ->numeric(decimalPlaces: 2)
                    ->alignEnd()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('evidence_path')
                    ->label('Evidence')
                    ->formatStateUsing(function ($state) {
                        if (empty($state)) {
                            return '—';
                        }

                        /** @var FilesystemAdapter $disk */
                        $disk = Storage::disk('s3');

                        $paths = is_array($state) ? $state : [$state];

                        $links = [];
                        foreach ($paths as $index => $path) {
                            if (! $path || ! $disk->exists($path)) {
                                continue;
                            }

                            $url      = $disk->temporaryUrl($path, now()->addMinutes(10));
                            $label    = 'File ' . ($index + 1);
                            $links[]  = '<a href="' . $url . '" target="_blank" class="text-primary-500 underline">' . $label . '</a>';
                        }

                        return $links ? implode(' &nbsp; ', $links) : '—';
                    })
                    ->html(),

                TextColumn::make('notes')
                    ->label('Notes')
                    ->limit(60)
                    ->tooltip(fn ($state) => $state)
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('creator.name')
                    ->label('Applied by')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Applied at')
                    ->dateTime()
                    ->sortable(),
            ]);
    }
}
