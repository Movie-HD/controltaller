<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Auth\Login;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use App\Filament\Pages\Dashboard;
use App\Filament\Widgets\BeneficiosChart;
use App\Filament\Widgets\GeneralChart;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

use Filament\View\PanelsRenderHook; # Se agrega para el Render Hook [Panel]
use Illuminate\Support\Facades\Blade; # Se agrego para los estilos CSS

class DashboardPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->maxContentWidth('full')
            ->id('dashboard')
            ->path('dashboard')
            ->renderHook(
                PanelsRenderHook::HEAD_END,
                fn () => Blade::render('
                <style>
                    .fi-main-ctn{
                        & > main{
                            padding: clamp(2px, calc(2px + .8vw), 15px);
                        }
                    }

                    .fi-wi-stats-overview-stats-ctn{
                        display: grid;
                        grid-template-columns: repeat(auto-fit, minmax(min(calc(50% - 3px), calc(120px + 10vw)), 1fr));
                        gap: clamp(7px, calc(2px + 1.4vw), 15px);

                        @media (width <= 940px) {
                            grid-template-columns: 1fr 1fr;
                            .fi-wi-stats-overview-stat{
                                padding: 1rem;
                            }
                        }
                        @media (width <= 640px) {
                            .fi-wi-stats-overview-stat{
                                padding: 0.5rem;
                            }
                        }
                        @media (width <= 480px) {
                            .fi-wi-stats-overview-stat{
                                padding: 0.35rem;
                            }
                            .fi-wi-stats-overview-stat-value{
                                font-size: 1.3rem;
                            }
                        }
                    }
                </style>')
            )
            ->login(Login::class)
            ->registration()
            ->topNavigation()
            ->breadcrumbs(false)
            ->brandName('Control Taller')
            ->plugins([
                FilamentShieldPlugin::make()
                    ->navigationGroup('Gestión')
                    ->navigationSort(5),
            ])
            ->colors([
                'primary' => Color::Amber,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                BeneficiosChart::class,
                GeneralChart::class,
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
