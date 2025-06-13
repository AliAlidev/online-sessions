<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run()
    {
        $permissions = [
            'create_event',
            'update_event',
            'delete_event',
            'list_events',
            'create_client',
            'update_client',
            'delete_client',
            'list_clients',
            'create_vendor',
            'update_vendor',
            'delete_vendor',
            'list_vendors',
            'create_folder',
            'update_folder',
            'delete_folder',
            'list_folders',
            'upload_image',
            'upload_video',
            'delete_image',
            'delete_video',
            'update_image',
            'update_video',
            'approve_decline_image',
            'approve_decline_video',
            'insights',
            'list_users',
            'create_user',
            'update_user',
            'delete_user',
            'create_event_type',
            'update_event_type',
            'delete_event_type',
            'list_event_types',
            'create_event_user',
            'update_event_user',
            'delete_event_user',
            'list_event_users',
            'create_client_user',
            'update_client_user',
            'delete_client_user',
            'list_clients_users'
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }
    }
}
