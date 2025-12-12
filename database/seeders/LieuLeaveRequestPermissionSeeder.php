<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Modules\Privilege\Models\Permission;

class LieuLeaveRequestPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $mainPermission = [
            'permission_name' => 'Lieu Leave Request',
            'guard_name' => 'manage-lieu-leave-request',
            'parent_id' => 0,
            'activated_at' => now(),
        ];

        $lieuLeaveRequestPermission = Permission::firstOrCreate(
            ['permission_name' => $mainPermission['permission_name']],
            $mainPermission
        );

        $subPermissions = [
            [
                'permission_name' => 'Approve Leiu Leave Request',
                'guard_name' => 'approve-lieu-leave-request',
                'parent_id' => $lieuLeaveRequestPermission->id,
                'activated_at' => now(),
            ],
            [
                'permission_name' => 'View Lieu Leave Request',
                'guard_name' => 'view-lieu-leave-request',
                'parent_id' => $lieuLeaveRequestPermission->id,
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
