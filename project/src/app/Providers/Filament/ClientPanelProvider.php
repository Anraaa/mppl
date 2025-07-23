<?php

namespace App\Providers\Filament;

use App\Filament\Client\Pages\Dashboard;
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
use Filament\Navigation\MenuItem;
use App\Filament\Client\Pages\StudioAvailability;
use App\Filament\Client\Resources\BookingResource;
use App\Filament\Client\Resources\BookingResource\Pages\PaymentBooking;
use App\Filament\Client\Resources\DashboardResource;
use App\Filament\Pages\Auth\EditProfile;
use App\Models\Payment;
use App\Http\Responses\Client\LoginResponse;
use Filament\Navigation\NavigationItem;
use Filament\Pages\Auth\EditProfile as AuthEditProfile;

class ClientPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('client')
            ->path('client')
            ->login()
            ->registration()
            ->maxContentWidth('full')
            ->passwordReset()
            ->breadcrumbs(false)
            ->spa()
            //->defaultRole('user') 
            ->colors([
                'primary' => Color::Emerald,
            ])
            ->userMenuItems([
                MenuItem::make()
                    ->label('Admin Panel')
                    ->icon('heroicon-o-cog-6-tooth')
                    ->url('/admin')
                    ->visible(fn(): bool => auth()->user()->hasRole('super_admin'))
            ])
            ->discoverResources(in: app_path('Filament/Client/Resources'), for: 'App\\Filament\\Client\\Resources')
            ->discoverPages(in: app_path('Filament/Client/Pages'), for: 'App\\Filament\\Client\\Pages')
            ->pages([
                Pages\Dashboard::class,
                StudioAvailability::class,
                /* PaymentBooking::class */
            ])
            ->discoverWidgets(in: app_path('Filament/Client/Widgets'), for: 'App\\Filament\\Client\\Widgets')
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
            ->favicon(asset('images/logo1.png'))
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->darkMode(false)
            ->authMiddleware([
                Authenticate::class,
            ]);
    }

    public function resources(): array
    {
        return [
            \App\Filament\Client\Resources\BookingResource::class,
        ];
    }
}
