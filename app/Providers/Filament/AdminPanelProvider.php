<?php

namespace App\Providers\Filament;

use Filament\Pages;
use Filament\Panel;
use Filament\Widgets;
use Filament\PanelProvider;
use Filament\Support\Assets\Js;
use Filament\Navigation\MenuItem;
use Filament\Support\Assets\Asset;
use Filament\Support\Colors\Color;
use App\Http\Responses\LogoutResponse;
use Filament\Http\Middleware\Authenticate;
use Filament\Support\Facades\FilamentAsset;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Filament\Http\Middleware\AuthenticateSession;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Filament\Http\Responses\Auth\Contracts\LogoutResponse as LogoutResponseContract;
use App\Filament\Widgets\StatsOverview;
use App\Filament\Widgets\AttendanceChart;
use App\Filament\Widgets\TodayAttendanceList;
use App\Filament\Widgets\TodayStaffAttendanceList;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->sidebarCollapsibleOnDesktop()
            ->id('admin')
            ->path('admin')
            ->userMenuItems([
                'logout' => MenuItem::make()->label('Log out'),
            ])
            ->login()
            ->colors([
                'primary' => Color::Amber,
            ])
            ->authGuard('web')
            ->authPasswordBroker('users')
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                // Widgets\AccountWidget::class,
                // Widgets\FilamentInfoWidget::class,
                StatsOverview::class,
                AttendanceChart::class,
                TodayAttendanceList::class,
                TodayStaffAttendanceList::class,
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
    
    public function register(): void
    {
        parent::register();
        
        // Bind custom LogoutResponse
        $this->app->bind(LogoutResponseContract::class, LogoutResponse::class);
        
        // Register QR code assets
        FilamentAsset::register([
            Js::make('qrcode', asset('js/app/qrcode.js')),
            Js::make('qrcode-scan', asset('js/app/qrcode-scan.js')),
        ]);
    }
}
