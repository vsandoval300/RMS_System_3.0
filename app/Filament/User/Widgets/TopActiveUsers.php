<?php

namespace App\Filament\User\Widgets;

use App\Models\User;
use Filament\Tables;
use Filament\Widgets\Widget;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;

class TopActiveUsers extends Widget 
{
    protected static string $view = 'filament.widgets.top-active-users';

    protected int|string|array $columnSpan = 'full';

    public function getTopUsers()
    {
        return User::withCount('loginLogs')
            ->orderByDesc('login_logs_count')
            ->limit(3)
            ->get();
    }
}