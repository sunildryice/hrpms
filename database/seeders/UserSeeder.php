<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Modules\Privilege\Models\Permission;
use Modules\Privilege\Models\Role;
use Modules\Privilege\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = new User();
        $user = $user->create([
            'id'=>1,
            'full_name' => 'Developer',
            'email_address' => 'developer@dryicesolutions.net',
            'password' => bcrypt('password78'),
            'activated_at' => date('Y-m-d H:i:s'),
        ]);
        $roles = Role::all()->pluck('id')->toArray();
        $permissions = Permission::all()->pluck('id')->toArray();
        $user->roles()->sync($roles);
        $user->permissions()->sync($permissions);
    }
}
