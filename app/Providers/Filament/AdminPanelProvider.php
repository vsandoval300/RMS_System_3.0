<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Facades\FilamentColor;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\Navigation\NavigationGroup;   // ✅ Asegúrate de tener esta línea

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {

        // ✅ Registro de colores personalizados
            FilamentColor::register([
                //'danger' => Color::Red,
                //'gray' => Color::Zinc,
                //'info' => Color::Blue,
                'primary' => Color::hex('#41A2C3'), // puedes registrar más
                //'success' => Color::Green,
                //'warning' => Color::Amber,
            ]);

        return $panel

            ->navigationGroups([
                NavigationGroup::make('Resources')->collapsed(),
                NavigationGroup::make('Banks')->collapsed(),
                NavigationGroup::make('Clients')->collapsed(),
                NavigationGroup::make('Reinsurers')->collapsed(),
                NavigationGroup::make('Underwritten')->collapsed(),
                NavigationGroup::make('Filament Shield')
                    ->collapsed(),
            ])
            ->default()
            ->brandName('RMS-System') // ← aquí se define el título
            ->brandLogo(asset('images/logo5.png'))
            ->brandLogoHeight('55px')   // ⬅︎ más alto que el default (~20 px)
            ->favicon(asset('images/result_favicon2.png'))  // ← AQUÍ
            ->id('admin')
            ->path('admin')
            ->login()
            ->plugins([
                \BezhanSalleh\FilamentShield\FilamentShieldPlugin::make(),
            ])
            /*->colors([
                'primary' => Color::Blue,
            ]) */
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            //->discoverClusters(in: app_path('Filament/Clusters'), for: 'App\\Filament\\Clusters') // ✅ ESTA ES LA NUEVA LÍNEA
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                Widgets\FilamentInfoWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}