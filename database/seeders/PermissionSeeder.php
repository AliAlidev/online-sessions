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
            'create_role',
            'update_role',
            'delete_role',
            'create_client',
            'update_client',
            'delete_client',
            'create_folder',
            'update_folder',
            'delete_folder',
            'upload_image',
            'upload_video',
            'delete_image',
            'delete_video',
            'update_image',
            'update_video',
            'approve_decline_image',
            'approve_decline_video',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }
    }
}
