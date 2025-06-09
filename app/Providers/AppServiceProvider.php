<?php

namespace App\Providers;

use App\Models\Client;
use App\Models\ClientRole;
use App\Models\Event;
use App\Models\EventFolder;
use App\Models\EventOrganizer;
use App\Models\EventSetting;
use App\Models\EventType;
use App\Models\FolderFile;
use App\Observers\ClientObserver;
use App\Observers\ClientRoleObserver;
use App\Observers\EventFolderObserver;
use App\Observers\EventObserver;
use App\Observers\EventOrganizerObserver;
use App\Observers\EventSettingObserver;
use App\Observers\EventTypeObserver;
use App\Observers\FolderFileObserver;
use Illuminate\Support\Facades\Schema;
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
        Schema::defaultStringLength(191);
        Event::observe(EventObserver::class);
        EventOrganizer::observe(EventOrganizerObserver::class);
        EventSetting::observe(EventSettingObserver::class);
        EventFolder::observe(EventFolderObserver::class);
        FolderFile::observe(FolderFileObserver::class);
        EventType::observe(EventTypeObserver::class);
        ClientRole::observe(ClientRoleObserver::class);
        Client::observe(ClientObserver::class);
    }
}
