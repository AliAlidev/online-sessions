<?php

namespace App\Observers;

use App\Models\Event;
use App\Models\EventLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class EventObserver
{
    /**
     * Handle the Event "created" event.
     */
    public function created(Event $event): void
    {
        //
    }

    /**
     * Handle the Event "updated" event.
     */
    public function updated(Event $event): void
    {
        try {
            EventLog::create([
                'data' => $event->getDirty(),
                'user_id' => Auth::id(),
                'table_name' => 'events',
                'action_type'=>'update',
                'table_id' => $event->id
            ]);
        } catch (\Throwable $th) {
            createServerError($th, "updateEventObserver", "events");
        }
    }

    /**
     * Handle the Event "deleted" event.
     */
    public function deleted(Event $event): void
    {
        try {
            EventLog::create([
                'data' => $event->toArray(),
                'user_id' => Auth::id(),
                'table_name' => 'events',
                'action_type'=>'delete',
                'table_id' => $event->id
            ]);
        } catch (\Throwable $th) {
            createServerError($th, "deleteEventObserver", "events");
        }
    }

    /**
     * Handle the Event "restored" event.
     */
    public function restored(Event $event): void
    {
        //
    }

    /**
     * Handle the Event "force deleted" event.
     */
    public function forceDeleted(Event $event): void
    {
        //
    }
}
