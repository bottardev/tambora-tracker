<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Hash;


class RolesSeeder extends Seeder
{
    public function run(): void
    {
        $perms = [
            'manage routes',
            'manage checkpoints',
            'manage hikers',
            'manage trips',
            'view dashboard',
            'view locations',
            'view events'
        ];
        foreach ($perms as $p) Permission::firstOrCreate(['name' => $p]);


        $admin = Role::firstOrCreate(['name' => 'admin']);
        $ranger = Role::firstOrCreate(['name' => 'ranger']);
        $hiker = Role::firstOrCreate(['name' => 'hiker']);


        $admin->givePermissionTo($perms);
        $ranger->givePermissionTo(['view dashboard', 'view locations', 'view events', 'manage trips']);
        $hiker->givePermissionTo([]);


        $user = User::firstOrCreate(['email' => 'admin@tambora.local'], [
            'name' => 'Admin',
            'password' => Hash::make('password')
        ]);
        $user->assignRole('admin');
    }
}
