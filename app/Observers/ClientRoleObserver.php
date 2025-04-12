<?php

namespace App\Observers;

use App\Models\ClientLog;
use App\Models\ClientRole;
use Illuminate\Support\Facades\Auth;

class ClientRoleObserver
{
    /**
     * Handle the ClientRole "created" event.
     */
    public function created(ClientRole $clientRole): void
    {
        try {
            ClientLog::create([
                'data' => $clientRole->all(),
                'user_id' => Auth::id(),
                'table_name' => 'client_roles',
                'action_type' => 'create',
                'table_id' => $clientRole->id
            ]);
        } catch (\Throwable $th) {
            createServerError($th, "createClientRoleObserver", "clients");
        }
    }

    /**
     * Handle the ClientRole "updated" event.
     */
    public function updated(ClientRole $clientRole): void
    {
        try {
            ClientLog::create([
                'data' => $clientRole->getDirty(),
                'user_id' => Auth::id(),
                'table_name' => 'client_roles',
                'action_type' => 'update',
                'table_id' => $clientRole->id
            ]);
        } catch (\Throwable $th) {
            createServerError($th, "updateClientRoleObserver", "clients");
        }
    }

    /**
     * Handle the ClientRole "deleted" event.
     */
    public function deleted(ClientRole $clientRole): void
    {
        try {
            ClientLog::create([
                'data' => $clientRole->toArray(),
                'user_id' => Auth::id(),
                'table_name' => 'client_roles',
                'action_type' => 'delete',
                'table_id' => $clientRole->id
            ]);
        } catch (\Throwable $th) {
            createServerError($th, "deleteClientRoleObserver", "clients");
        }
    }

    /**
     * Handle the ClientRole "restored" event.
     */
    public function restored(ClientRole $clientRole): void
    {
        //
    }

    /**
     * Handle the ClientRole "force deleted" event.
     */
    public function forceDeleted(ClientRole $clientRole): void
    {
        //
    }
}
