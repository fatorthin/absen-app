<?php

namespace App\Providers;

use Filament\Support\Assets\Js;
use Illuminate\Support\ServiceProvider;
use Filament\Support\Facades\FilamentAsset;
use App\Models\Event;
use App\Observers\EventObserver;
use App\Http\Responses\LogoutResponse;
use Filament\Http\Responses\Auth\Contracts\LogoutResponse as LogoutResponseContract;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
         $this->app->bind(LogoutResponseContract::class, LogoutResponse::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        FilamentAsset::register([
            Js::make('qrcode', 'https://unpkg.com/html5-qrcode'),
            Js::make('qrcode-scan', __DIR__ . '/../../resources/js/qrcode-scan.js')->loadedOnRequest(),
        ]);
        
        Event::observe(EventObserver::class);
    }
}
