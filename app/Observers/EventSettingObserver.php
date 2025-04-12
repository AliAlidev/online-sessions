<?php

namespace App\Observers;

use App\Models\EventLog;
use App\Models\EventSetting;
use Illuminate\Support\Facades\Auth;

class EventSettingObserver
{
    /**
     * Handle the EventSetting "created" event.
     */
    public function created(EventSetting $eventSetting): void
    {
        try {
            EventLog::create([
                'data' => $eventSetting->getDirty(),
                'user_id' => Auth::id(),
                'table_name' => 'event_settings',
                'table_id' => $eventSetting->event_id,
                'action_type'=>'create'
            ]);
        } catch (\Throwable $th) {
            createServerError($th, "createEventSettingObserver", "events");
        }
    }

    /**
     * Handle the EventSetting "updated" event.
     */
    public function updated(EventSetting $eventSetting): void
    {
        try {
            EventLog::create([
                'data' => $eventSetting->getDirty(),
                'user_id' => Auth::id(),
                'table_name' => 'event_settings',
                'table_id' => $eventSetting->event_id,
                'action_type'=>'update'
            ]);
        } catch (\Throwable $th) {
            createServerError($th, "updateEventSettingObserver", "events");
        }
    }

    /**
     * Handle the EventSetting "deleted" event.
     */
    public function deleted(EventSetting $eventSetting): void
    {
        try {
            EventLog::create([
                'data' => $eventSetting->toArray(),
                'user_id' => Auth::id(),
                'table_name' => 'event_settings',
                'table_id' => $eventSetting->event_id,
                'action_type'=>'delete'
            ]);
        } catch (\Throwable $th) {
            createServerError($th, "updateEventSettingObserver", "events");
        }
    }

    /**
     * Handle the EventSetting "restored" event.
     */
    public function restored(EventSetting $eventSetting): void
    {
        //
    }

    /**
     * Handle the EventSetting "force deleted" event.
     */
    public function forceDeleted(EventSetting $eventSetting): void
    {
        //
    }
}
