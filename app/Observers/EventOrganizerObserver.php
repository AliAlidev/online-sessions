<?php

namespace App\Observers;

use App\Models\EventLog;
use App\Models\EventOrganizer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class EventOrganizerObserver
{
    /**
     * Handle the EventOrganizer "created" event.
     */
    public function created(EventOrganizer $eventOrganizer): void
    {
        try {
            EventLog::create([
                'data' => $eventOrganizer->getDirty(),
                'user_id' => Auth::id(),
                'table_name' => 'event_organizers',
                'table_id' => $eventOrganizer->event_id,
                'action_type'=>'create'
            ]);
        } catch (\Throwable $th) {
            createServerError($th, "createEventOrganizersObserver", "events");
        }
    }

    /**
     * Handle the EventOrganizer "updated" event.
     */
    public function updated(EventOrganizer $eventOrganizer): void
    {
        try {
            EventLog::create([
                'data' => $eventOrganizer->getDirty(),
                'user_id' => Auth::id(),
                'table_name' => 'event_organizers',
                'table_id' => $eventOrganizer->event_id,
                'action_type'=>'update'
            ]);
        } catch (\Throwable $th) {
            createServerError($th, "updateEventOrganizersObserver", "events");
        }
    }

    /**
     * Handle the EventOrganizer "deleted" event.
     */
    public function deleted(EventOrganizer $eventOrganizer): void
    {
        try {
            EventLog::create([
                'data' => $eventOrganizer->toArray(),
                'user_id' => Auth::id(),
                'table_name' => 'event_organizers',
                'table_id' => $eventOrganizer->event_id,
                'action_type'=>'delete'
            ]);
        } catch (\Throwable $th) {
            createServerError($th, "deleteEventOrganizersObserver", "events");
        }
    }

    /**
     * Handle the EventOrganizer "restored" event.
     */
    public function restored(EventOrganizer $eventOrganizer): void
    {
        //
    }

    /**
     * Handle the EventOrganizer "force deleted" event.
     */
    public function forceDeleted(EventOrganizer $eventOrganizer): void
    {
        //
    }
}
