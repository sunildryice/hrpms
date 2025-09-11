<?php

namespace Database\Seeders;

use DB;
use Illuminate\Database\Seeder;

class ClearanceDepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $activated = now();
        DB::table('lkup_staff_clearance_departments')->upsert([
            ['id' => '1', 'title' => 'Logistic', 'activated_at' => $activated, 'parent_id' => '0'],
            ['id' => '2', 'title' => 'HR/Admin', 'activated_at' => $activated, 'parent_id' => '0'],
            ['id' => '3', 'title' => 'Finance', 'activated_at' => $activated, 'parent_id' => '0'],
            ['id' => '4', 'title' => 'Computers& accessories', 'activated_at' => $activated, 'parent_id' => '1'],
            ['id' => '5', 'title' => 'other Equipment', 'activated_at' => $activated, 'parent_id' => '1'],
            ['id' => '6', 'title' => 'Verified by IT Consultant', 'activated_at' => $activated, 'parent_id' => '1'],
            ['id' => '7', 'title' => 'Final Attendance', 'activated_at' => $activated, 'parent_id' => '2'],
            ['id' => '8', 'title' => 'Exit interview signed form', 'activated_at' => $activated, 'parent_id' => '2'],
            ['id' => '9', 'title' => 'ID card', 'activated_at' => $activated, 'parent_id' => '2'],
            ['id' => '10', 'title' => 'Outstanding Advances', 'activated_at' => $activated, 'parent_id' => '3'],
            ['id' => '11', 'title' => 'Loans', 'activated_at' => $activated, 'parent_id' => '3'],
            ['id' => '12', 'title' => 'Other payables', 'activated_at' => $activated, 'parent_id' => '3'],
        ], ['id', 'title'], ['title', 'activated_at', 'parent_id']);
    }
}
