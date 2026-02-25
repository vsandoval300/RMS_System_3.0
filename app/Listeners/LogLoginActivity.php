<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Auth\Events\Login;
use App\Models\LoginLog;

class LogLoginActivity
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(Login $event)
    {
       LoginLog::create([
        'user_id' => $event->user->id,
        'logged_in_at' => now(),
        'ip_address' => request()->ip(),
        'user_agent' => request()->userAgent(),
       ]);
    }
}
