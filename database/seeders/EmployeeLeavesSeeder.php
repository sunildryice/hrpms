<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EmployeeLeavesSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('employee_leaves')->insert([
            [
                'employee_id' => 12,
                'fiscal_year_id' => 4,
                'leave_type_id' => 3,
                'reported_date' => '2025-11-01',
                'opening_balance' => 0.0,
                'earned' => 4.0,
                'taken' => 4.0,
                'paid' => 0.0,
                'lapsed' => 0.0,
                'balance' => 0.0,
                'remarks' => 'New leave earned/reconciled.',
                'created_at' => '2025-11-19 10:37:19',
                'updated_at' => '2025-11-20 10:16:15',
            ],
            [
                'employee_id' => 12,
                'fiscal_year_id' => 4,
                'leave_type_id' => 6,
                'reported_date' => '2025-11-01',
                'opening_balance' => 0.0,
                'earned' => 8.0,
                'taken' => 8.0,
                'paid' => 0.0,
                'lapsed' => 0.0,
                'balance' => 0.0,
                'remarks' => 'New leave earned/reconciled.',
                'created_at' => '2025-11-19 10:37:19',
                'updated_at' => '2025-11-21 15:02:52',
            ],
            [
                'employee_id' => 12,
                'fiscal_year_id' => 4,
                'leave_type_id' => 12,
                'reported_date' => '2025-11-01',
                'opening_balance' => 0.0,
                'earned' => 0.5,
                'taken' => 0.0,
                'paid' => 0.0,
                'lapsed' => 0.0,
                'balance' => 0.5,
                'remarks' => 'New leave earned.',
                'created_at' => '2025-11-19 10:37:19',
                'updated_at' => '2025-11-19 10:37:19',
            ],
            [
                'employee_id' => 12,
                'fiscal_year_id' => 4,
                'leave_type_id' => 18,
                'reported_date' => '2025-11-01',
                'opening_balance' => 0.0,
                'earned' => 6.5,
                'taken' => 0.0,
                'paid' => 0.0,
                'lapsed' => 0.0,
                'balance' => 6.5,
                'remarks' => 'New leave earned.',
                'created_at' => '2025-11-19 10:37:19',
                'updated_at' => '2025-11-19 10:37:19',
            ],
            [
                'employee_id' => 12,
                'fiscal_year_id' => 4,
                'leave_type_id' => 21,
                'reported_date' => '2025-11-01',
                'opening_balance' => 0.0,
                'earned' => 3.5,
                'taken' => 0.0,
                'paid' => 0.0,
                'lapsed' => 0.0,
                'balance' => 3.5,
                'remarks' => 'New leave earned.',
                'created_at' => '2025-11-19 10:37:19',
                'updated_at' => '2025-11-19 10:37:19',
            ],
            [
                'employee_id' => 12,
                'fiscal_year_id' => 4,
                'leave_type_id' => 24,
                'reported_date' => '2025-11-01',
                'opening_balance' => 0.0,
                'earned' => 0.0,
                'taken' => 0.0,
                'paid' => 0.0,
                'lapsed' => 0.0,
                'balance' => 0.0,
                'remarks' => 'New leave earned.',
                'created_at' => '2025-11-19 10:37:19',
                'updated_at' => '2025-11-19 10:37:19',
            ],
        ]);
    }
}
