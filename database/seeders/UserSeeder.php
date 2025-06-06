<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
       $user = User::firstOrCreate([
            'email' => 'sadmin@app.com'
        ],[
            'name' => 'sadmin',
            'email' => 'sadmin@app.com',
            'password' => Hash::make('12345'),
            'user_type' => 'super-admin'
        ]);
        $permissions = Permission::all();
        $user->syncPermissions($permissions);
        $user->assignRole('super-admin');
    }
}
