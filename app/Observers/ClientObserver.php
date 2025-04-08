<?php

namespace App\Observers;

use App\Models\Client;
use App\Models\ClientLog;
use Illuminate\Support\Facades\Auth;

class ClientObserver
{
    /**
     * Handle the Client "created" event.
     */
    public function created(Client $client): void
    {
        try {
            ClientLog::create([
                'data' => $client->all(),
                'user_id' => Auth::id(),
                'table_name' => 'clients',
                'action_type' => 'create',
                'table_id' => $client->id
            ]);
        } catch (\Throwable $th) {
            createServerError($th, "createClientObserver", "clients");
        }
    }

    /**
     * Handle the Client "updated" event.
     */
    public function updated(Client $client): void
    {
        try {
            ClientLog::create([
                'data' => $client->getDirty(),
                'user_id' => Auth::id(),
                'table_name' => 'clients',
                'action_type' => 'update',
                'table_id' => $client->id
            ]);
        } catch (\Throwable $th) {
            createServerError($th, "updateClientObserver", "clients");
        }
    }

    /**
     * Handle the Client "deleted" event.
     */
    public function deleted(Client $client): void
    {
        try {
            ClientLog::create([
                'data' => $client->toArray(),
                'user_id' => Auth::id(),
                'table_name' => 'clients',
                'action_type' => 'delete',
                'table_id' => $client->id
            ]);
        } catch (\Throwable $th) {
            createServerError($th, "deleteClientObserver", "clients");
        }
    }

    /**
     * Handle the Client "restored" event.
     */
    public function restored(Client $client): void
    {
        //
    }

    /**
     * Handle the Client "force deleted" event.
     */
    public function forceDeleted(Client $client): void
    {
        //
    }
}
