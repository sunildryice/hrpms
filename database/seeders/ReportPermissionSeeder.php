<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Privilege\Models\Permission;

class ReportPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $mainPermission = [
            'permission_name' => 'View Report',
            'guard_name' => 'view-report',
            'parent_id' => 0,
            'activated_at' => now(),
        ];

        $reportPermission = Permission::firstOrCreate(
            ['permission_name' => $mainPermission['permission_name']],
            $mainPermission
        );

        $subPermissions = [
            [
                'permission_name' => 'Work From Home Report',
                'guard_name' => 'work-from-home-report',
                'parent_id' => $reportPermission->id,
                'activated_at' => now(),
            ],
            [
                'permission_name' => 'Off Day Work Report',
                'guard_name' => 'off-day-work-report',
                'parent_id' => $reportPermission->id,
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
