<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Tarea programada para actualizar Status de Business:
Schedule::command('businesses:refresh-lifecycle-statuses')
    ->dailyAt('02:00');

// Digest semanal de aprobaciones pendientes — lunes 8:00am:
Schedule::command('rms:send-approvals-digest')
    ->weeklyOn(1, '08:00')
    ->withoutOverlapping();
