<?php

namespace App\Providers\Filament;

use Althinect\FilamentSpatieRolesPermissions\FilamentSpatieRolesPermissionsPlugin;
use App\Filament\Business\Resources\CustomerResource;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;
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

class BusinessPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('business')
            ->path('business')
            ->colors([
                'primary' => Color::Amber,
            ])
            ->login(\App\Filament\Auth\Login::class) 
            ->discoverResources(in: app_path('Filament/Business/Resources'), for: 'App\\Filament\\Business\\Resources')
            ->discoverPages(in: app_path('Filament/Business/Pages'), for: 'App\\Filament\\Business\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->plugin(
                \Hasnayeen\Themes\ThemesPlugin::make()
            )
            ->brandName('SplitPay')
            ->navigationGroups([
                // NavigationGroup::make()
                //     ->label('Филиалы')
                //     ->icon('heroicon-o-rectangle-stack'),
                // NavigationGroup::make()
                //     ->label('Контракты')
                //     ->icon('heroicon-o-rectangle-stack'),
                // NavigationGroup::make()
                //     ->label('Клиенты')
                //     ->icon('heroicon-o-rectangle-stack'),
            ])
            ->plugin(FilamentSpatieRolesPermissionsPlugin::make())
            ->discoverWidgets(in: app_path('Filament/Business/Widgets'), for: 'App\\Filament\\Business\\Widgets')
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
            ])
            ->middleware([
                \Hasnayeen\Themes\Http\Middleware\SetTheme::class
            ])
            // or in `tenantMiddleware` if you're using multi-tenancy
            ->tenantMiddleware([
                \Hasnayeen\Themes\Http\Middleware\SetTheme::class
            ])
            ->authGuard('business');
    }
}
