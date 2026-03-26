<?php

namespace App\Filament\User\Widgets;

use Filament\Widgets\TableWidget;
use Illuminate\Support\Facades\DB;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;

class TopActiveUsers extends TableWidget
{
    protected static ?string $heading = 'Top Active Users';

    protected function getTableQuery(): Builder
    {
        return \App\Models\User::query()
            ->select('users.*')
            ->selectSub(function ($q) {
                $q->from('login_logs')
                    ->selectRaw('COUNT(*)')
                    ->whereColumn('login_logs.user_id', 'users.id')
                    ->where('logged_in_at', '>=', now()->subDays(30));
            }, 'total_logins')
            ->orderByDesc('total_logins')
            ->limit(5);
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('name')->searchable(),
            Tables\Columns\TextColumn::make('email')->searchable(),
            Tables\Columns\TextColumn::make('total_logins')
                ->badge()
                ->color('success')
        ];
    }
}