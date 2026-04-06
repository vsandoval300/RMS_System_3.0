<?php

namespace App\Filament\User\Widgets;

use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\Action;
use Carbon\Carbon;

class AtRiskUsers extends TableWidget
{
    protected static ?string $heading = 'At Risk Users';

    protected function getTableQuery(): Builder
    {
        return \App\Models\User::query()
            ->leftJoin('login_logs', 'login_logs.user_id', '=', 'users.id')
            ->select('users.*')
            ->selectRaw('MAX(login_logs.created_at) as last_login')
            ->groupBy('users.id')
            //->havingRaw('MAX(login_logs.created_at) < NOW() - INTERVAL \'7 days\' OR MAX(login_logs.created_at) IS NULL')
            ->havingRaw("
                MAX(login_logs.created_at) IS NULL 
                OR MAX(login_logs.created_at) < NOW() - INTERVAL '7 days'
            ")
            //->orderBy('last_login', 'asc')
            ->orderByRaw('MAX(login_logs.created_at) ASC NULLS FIRST')
            ->limit(10);
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('name')
                ->label('Name')
                ->searchable()
                ->weight('medium'),

            TextColumn::make('email')
                ->label('Email')
                ->copyable(),

            TextColumn::make('last_login')
                ->label('Last login')
                ->dateTime('d M Y H:i')
                ->placeholder('Never'),

            // 🔥 Columna clave
            TextColumn::make('days_since_last_login')
                ->label('Days of inactivity')
                ->getStateUsing(function ($record) {
                    if (!$record->last_login) {
                        return 'Never';
                    }

                    return Carbon::parse($record->last_login)
                        ->diffInDays(now());
                })
                ->badge()
                ->color(function ($state) {
                    if ($state === 'Never') return 'danger';

                    return match (true) {
                        $state <= 7 => 'success',
                        $state <= 14 => 'warning',
                        default => 'danger',
                    };
                }),
        ];
    }
}