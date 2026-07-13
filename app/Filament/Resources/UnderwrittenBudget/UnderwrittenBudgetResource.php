<?php

namespace App\Filament\Resources\UnderwrittenBudget;

use App\Filament\Resources\UnderwrittenBudget\Pages\BudgetBatchCapture;
use App\Filament\Resources\UnderwrittenBudget\Pages\EditBudgetVersion;
use App\Filament\Resources\UnderwrittenBudget\Pages\ListUnderwrittenBudgets;
use App\Filament\Resources\UnderwrittenBudget\Pages\ViewBudgetVersion;
use App\Models\UnderwrittenBudget as UnderwrittenBudgetModel;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\Action;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class UnderwrittenBudgetResource extends Resource
{
    protected static ?string $model = UnderwrittenBudgetModel::class;

    protected static string|\BackedEnum|null $navigationIcon  = 'heroicon-o-minus';
    protected static string|\UnitEnum|null   $navigationGroup = 'Underwritten';
    protected static ?string                 $navigationLabel = 'Budget';
    protected static ?string                 $modelLabel      = 'Budget Version';
    protected static ?string                 $pluralModelLabel = 'Budget';
    protected static ?int                    $navigationSort  = 4;

    public static function getNavigationBadge(): ?string
    {
        return (string) UnderwrittenBudgetModel::count() ?: null;
    }

    // ── Form (not used — creation/edit via custom pages) ──

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    // ── Table ──────────────────────────────────────────────

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('year', 'desc')
            ->columns([
                TextColumn::make('year')
                    ->label('Year')
                    ->sortable()
                    ->badge()
                    ->color('info'),

                TextColumn::make('version')
                    ->label('Version')
                    ->formatStateUsing(fn ($state) => 'v' . $state)
                    ->badge()
                    ->color(fn ($state) => match (true) {
                        $state === 1 => 'gray',
                        $state === 2 => 'warning',
                        default      => 'success',
                    })
                    ->sortable(),

                TextColumn::make('label')
                    ->label('Version Label')
                    ->searchable(),

                TextColumn::make('items_count')
                    ->label('Reinsurers')
                    ->counts('items')
                    ->badge()
                    ->color('gray')
                    ->alignCenter(),

                TextColumn::make('total_budget')
                    ->label('Total Budget (USD)')
                    ->getStateUsing(fn ($record) => '$' . number_format($record->items()->sum('premium_budget'), 2))
                    ->alignRight()
                    ->fontFamily('mono'),

                TextColumn::make('creator.name')
                    ->label('Created by')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Date')
                    ->date('M d, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('year')
                    ->label('Year')
                    ->options(
                        collect(range(now()->year - 3, now()->year + 1))
                            ->mapWithKeys(fn ($y) => [$y => (string) $y])
                            ->toArray()
                    ),
            ])
            ->recordAction(null)
            ->recordUrl(null)
            ->recordActions([
                ActionGroup::make([
                    Action::make('view_version')
                        ->label('View')
                        ->icon('heroicon-m-eye')
                        ->color('gray')
                        ->url(fn ($record) => static::getUrl('view-version', ['record' => $record])),

                    Action::make('edit_version')
                        ->label('Edit')
                        ->icon('heroicon-m-pencil-square')
                        ->color('primary')
                        ->url(fn ($record) => static::getUrl('edit-version', ['record' => $record])),

                    DeleteAction::make(),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'        => ListUnderwrittenBudgets::route('/'),
            'batch'        => BudgetBatchCapture::route('/batch'),
            'view-version' => ViewBudgetVersion::route('/{record}'),
            'edit-version' => EditBudgetVersion::route('/{record}/edit'),
        ];
    }
}
