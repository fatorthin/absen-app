<?php

namespace App\Providers;

use Filament\Support\Assets\Js;
use Illuminate\Support\ServiceProvider;
use Filament\Support\Facades\FilamentAsset;
use App\Models\Event;
use App\Observers\EventObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
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
