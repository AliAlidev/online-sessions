<?php

namespace App\Observers;

use App\Models\EventLog;
use App\Models\FolderFile;
use Illuminate\Support\Facades\Auth;

class FolderFileObserver
{
    /**
     * Handle the FolderFile "created" event.
     */
    public function created(FolderFile $folderFile): void
    {
        try {
            EventLog::create([
                'data' => $folderFile->all(),
                'user_id' => Auth::id(),
                'table_name' => 'folder_files',
                'table_id' => $folderFile->folder->event_id,
                'action_type' => 'create'
            ]);
        } catch (\Throwable $th) {
            createServerError($th, "createFolderFileObserver", "events");
        }
    }

    /**
     * Handle the FolderFile "updated" event.
     */
    public function updated(FolderFile $folderFile): void
    {
        try {
            EventLog::create([
                'data' => $folderFile->getDirty(),
                'user_id' => Auth::id(),
                'table_name' => 'folder_files',
                'table_id' => $folderFile->folder->event_id,
                'action_type' => 'update'
            ]);
        } catch (\Throwable $th) {
            createServerError($th, "updateFolderFileObserver", "events");
        }
    }

    /**
     * Handle the FolderFile "deleted" event.
     */
    public function deleted(FolderFile $folderFile): void
    {
        try {
            EventLog::create([
                'data' => $folderFile->toArray(),
                'user_id' => Auth::id(),
                'table_name' => 'folder_files',
                'table_id' => $folderFile->folder->event_id,
                'action_type' => 'delete'
            ]);
        } catch (\Throwable $th) {
            createServerError($th, "deleteFolderFileObserver", "events");
        }
    }

    /**
     * Handle the FolderFile "restored" event.
     */
    public function restored(FolderFile $folderFile): void
    {
        //
    }

    /**
     * Handle the FolderFile "force deleted" event.
     */
    public function forceDeleted(FolderFile $folderFile): void
    {
        //
    }
}
