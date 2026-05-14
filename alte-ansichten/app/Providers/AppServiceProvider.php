<?php

namespace App\Providers;

use App\Models\Municipality;
use App\Observers\MunicipalityObserver;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Municipality::observe(MunicipalityObserver::class);

        Event::listen(Login::class, function ($event) {
            $event->user->update(['last_login_at' => now()]);
        });

        $appUrl = config('app.url');

        if ($appUrl) {
            URL::forceRootUrl($appUrl);

            if (str_starts_with($appUrl, 'https://')) {
                URL::forceScheme('https');
            }
        }
    }
}
