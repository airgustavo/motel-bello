<?php

namespace App\Providers\Filament;

use App\Filament\Pages\RoomOverview; // Asegúrate de importar esta clase
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
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
use Althinect\FilamentSpatieRolesPermissions\FilamentSpatieRolesPermissionsPlugin;


class DashboardPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->brandName('Motel Bello')
            ->id('dashboard')
            ->path('dashboard')
            ->login()
            ->colors([
                'primary' => Color::Amber,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->pages([
                \App\Filament\Pages\RoomOverview::class, // ✅ OK
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                //Widgets\AccountWidget::class,
                //Widgets\IncomeTodayWidget::class, // Asegúrate de que este widget esté correctamente definido
                //Widgets\RoomsRentalsTodayWidget::class, // Asegúrate de que este widget esté correctamente definido
                // Widgets\FilamentInfoWidget::class,
            ])
            ->profile()
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
            //->plugin(FilamentSpatieRolesPermissionsPlugin::make())
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
