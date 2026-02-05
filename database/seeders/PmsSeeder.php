<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Privilege\Models\Permission;

class PmsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $mainPermission = [
            'permission_name' => 'PMS Management',
            'guard_name' => 'manage-pms',
            'parent_id' => 0,
            'activated_at' => now(),
        ];

        $pmsPermission = Permission::firstOrCreate(
            ['permission_name' => $mainPermission['permission_name']],
            $mainPermission
        );

        $subPermissions = [
            [
                'permission_name' => 'Manage Project Activities',
                'guard_name' => 'manage-project-activities',
                'parent_id' => $pmsPermission->id,
                'activated_at' => now(),
            ],
            [
                'permission_name' => 'Manage Activity Stages',
                'guard_name' => 'manage-activity-stages',
                'parent_id' => $pmsPermission->id,
                'activated_at' => now(),
            ],
            [
                'permission_name' => 'Manage Activity Update Periods',
                'guard_name' => 'manage-activity-update-periods',
                'parent_id' => $pmsPermission->id,
                'activated_at' => now(),
            ],
            [
                'permission_name' => 'Employee Work Plans',
                'guard_name' => 'employee-work-plans',
                'parent_id' => $pmsPermission->id,
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
