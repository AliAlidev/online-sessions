<?php

namespace App\Observers;

use App\Models\EventLog;
use App\Models\EventType;
use Illuminate\Support\Facades\Auth;

class EventTypeObserver
{
    /**
     * Handle the EventType "created" event.
     */
    public function created(EventType $eventType): void
    {
        //
    }

    /**
     * Handle the EventType "updated" event.
     */
    public function updated(EventType $eventType): void
    {
        try {
            EventLog::create([
                'data' => $eventType->getDirty(),
                'user_id' => Auth::id(),
                'table_name' => 'event_types',
                'table_id' => $eventType->id,
                'action_type'=>'update'
            ]);
        } catch (\Throwable $th) {
            createServerError($th, "updateEventTypeObserver", "events");
        }
    }

    /**
     * Handle the EventType "deleted" event.
     */
    public function deleted(EventType $eventType): void
    {
        try {
            EventLog::create([
                'data' => $eventType->toArray(),
                'user_id' => Auth::id(),
                'table_name' => 'event_types',
                'table_id' => $eventType->id,
                'action_type'=>'delete'
            ]);
        } catch (\Throwable $th) {
            createServerError($th, "deleteEventTypeObserver", "events");
        }
    }

    /**
     * Handle the EventType "restored" event.
     */
    public function restored(EventType $eventType): void
    {
        //
    }

    /**
     * Handle the EventType "force deleted" event.
     */
    public function forceDeleted(EventType $eventType): void
    {
        //
    }
}
