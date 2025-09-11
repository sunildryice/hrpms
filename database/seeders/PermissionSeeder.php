<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use DB;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $query = "INSERT INTO `permissions` (`id`, `parent_id`, `permission_name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 0, 'Manage Privilege', 'manage-privilege', '2022-03-15 05:58:40', '2022-03-15 05:59:05'),
(2, 0, 'Manage Configuration', 'manage-configuration', '2022-03-15 05:58:40', '2022-03-15 05:59:05'),
(3, 0, 'Purchase Requests', 'purchase-request', '2022-03-15 05:58:40', '2022-03-15 05:59:05'),
(4, 3, 'Add Purchase Requests', 'add-purchase-request', '2022-03-15 05:58:40', '2022-03-15 05:59:05'),
(5, 3, 'Finance Review', 'finance-review-pr', '2022-03-15 05:58:40', '2022-03-15 05:59:05'),
(6, 3, 'Approve Purchase Requests', 'approve-purchase-request', '2022-03-15 05:58:40', '2022-03-15 05:59:05'),
(7, 3, 'Procurement Review', 'procurement-review-pr', '2022-03-15 05:58:40', '2022-03-15 05:59:05'),
(8, 1, 'Manage Roles', 'manage-role', '2022-03-15 05:58:40', '2022-03-15 05:59:05'),
(9, 1, 'Manage Users', 'manage-user', '2022-03-15 05:58:40', '2022-03-15 05:59:05'),
(10, 2, 'Manage Office', 'manage-office', '2022-03-15 05:58:40', '2022-03-15 05:59:05'),
(11, 2, 'Manage Unit', 'manage-unit', '2022-03-15 05:58:40', '2022-03-15 05:59:05'),
(12, 2, 'Manage Department', 'manage-department', '2022-03-15 05:58:40', '2022-03-15 05:59:05'),
(13, 2, 'Manage Categories', 'manage-category', '2022-03-15 05:58:40', '2022-03-15 05:59:05'),
(14, 2, 'Manage Account Codes', 'manage-account-code', '2022-03-15 05:58:40', '2022-03-15 05:59:05'),
(15, 2, 'Manage Item', 'manage-item', '2022-03-15 05:58:40', '2022-03-15 05:59:05'),
(16, 3, 'IT Review', 'it-review-pr', '2022-03-15 05:58:40', '2022-03-15 05:59:05'),
(17, 1, 'Manage Permission', 'manage-permission', '2022-03-15 05:58:40', '2022-03-15 05:59:05');
";

        DB::insert($query);
    }
}
