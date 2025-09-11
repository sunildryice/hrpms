<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Modules\Privilege\Models\Permission;
use Modules\Privilege\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $role = new Role();
        $role = $role->create(['role' => 'Developer', 'id'=>1]);
        $permissions = Permission::all()->pluck('id')->toArray();
        $role->permissions()->sync($permissions);

        $role = $role->create(['role' => 'Admin', 'id'=>2]);
        $role = $role->create(['role' => 'Normal User', 'id'=>3]);
    }
}
