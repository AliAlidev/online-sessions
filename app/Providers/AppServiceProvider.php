<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

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
    public function boot()
    {
        if (!app()->runningInConsole()) {
            view()->composer('*', function ($view) {
                $events = getEventsIncreasingCount();
                $clients = getClientsIncreasingCount();
                $view->with([
                    'header_events_percentage_value' => $events['percentage'],
                    'header_events_percentage_color' => $events['color'],
                    'header_events_percentage_sign' => $events['sign'],
                    'header_clients_percentage_value' => $clients['percentage'],
                    'header_clients_percentage_color' => $clients['color'],
                    'header_clients_percentage_sign' => $clients['sign']
                ]);
            });
        }
    }
}
