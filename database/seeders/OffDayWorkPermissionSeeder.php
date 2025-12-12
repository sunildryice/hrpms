<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Modules\Privilege\Models\Permission;

class OffDayWorkPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $mainPermission = [
            'permission_name' => 'Off Day Work',
            'guard_name' => 'manage-off-day-work',
            'parent_id' => 0,
            'activated_at' => now(),
        ];

        $offDayWorkPermission = Permission::firstOrCreate(
            ['permission_name' => $mainPermission['permission_name']],
            $mainPermission
        );

        $subPermissions = [
            [
                'permission_name' => 'Approve Off Day Work',
                'guard_name' => 'approve-off-day-work',
                'parent_id' => $offDayWorkPermission->id,
                'activated_at' => now(),
            ],
            [
                'permission_name' => 'View Off Day Work',
                'guard_name' => 'view-off-day-work',
                'parent_id' => $offDayWorkPermission->id,
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
