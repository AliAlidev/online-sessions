<?php

namespace App\Observers;

use App\Models\EventFolder;
use App\Models\EventLog;
use Illuminate\Support\Facades\Auth;

class EventFolderObserver
{
    /**
     * Handle the EventFolder "created" event.
     */
    public function created(EventFolder $eventFolder): void
    {
        //
    }

    /**
     * Handle the EventFolder "updated" event.
     */
    public function updated(EventFolder $eventFolder): void
    {
        try {
            EventLog::create([
                'data' => $eventFolder->getDirty(),
                'user_id' => Auth::id(),
                'table_name' => 'event_folders',
                'action_type'=>'update',
                'table_id' => $eventFolder->event_id
            ]);
        } catch (\Throwable $th) {
            createServerError($th, "updateEventFolderObserver", "events");
        }
    }

    /**
     * Handle the EventFolder "deleted" event.
     */
    public function deleted(EventFolder $eventFolder): void
    {
        try {
            EventLog::create([
                'data' => $eventFolder->toArray(),
                'user_id' => Auth::id(),
                'table_name' => 'event_folders',
                'action_type'=>'update',
                'table_id' => $eventFolder->event_id
            ]);
        } catch (\Throwable $th) {
            createServerError($th, "deleteEventFolderObserver", "events");
        }
    }

    /**
     * Handle the EventFolder "restored" event.
     */
    public function restored(EventFolder $eventFolder): void
    {
        //
    }

    /**
     * Handle the EventFolder "force deleted" event.
     */
    public function forceDeleted(EventFolder $eventFolder): void
    {
        //
    }
}
