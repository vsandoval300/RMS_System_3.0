<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Model;
use Filament\Http\Responses\Auth\Contracts\LoginResponse;
use App\Http\Responses\CustomLoginResponse;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
        $this->app->bind(LoginResponse::class, CustomLoginResponse::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
        Model::unguard();
        // 👇 registra el observer
        //OperativeDoc::observe(OperativeDocObserver::class);
    }
}
