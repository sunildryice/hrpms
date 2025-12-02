<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Modules\Privilege\Models\Permission;

class WorkFromHomePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $mainPermission = [
            'permission_name' => 'Work From Home',
            'guard_name' => 'work-from-home-request',
            'parent_id' => 0,
            'activated_at' => now(),
        ];

        $workFromHomePermission = Permission::firstOrCreate(
            ['permission_name' => $mainPermission['permission_name']],
            $mainPermission
        );

        $subPermissions = [
            [
                'permission_name' => 'Approve Work From Home',
                'guard_name' => 'approve-work-from-home',
                'parent_id' => $workFromHomePermission->id,
                'activated_at' => now(),
            ],
            [
                'permission_name' => 'View Work From Home',
                'guard_name' => 'view-work-from-home',
                'parent_id' => $workFromHomePermission->id,
                'activated_at' => now(),
            ],
        ];

        foreach ($subPermissions as $subPermission) {
            Permission::firstOrCreate(
                ['permission_name' => $subPermission['permission_name']],
                $subPermission
            );
        }
    }
}
