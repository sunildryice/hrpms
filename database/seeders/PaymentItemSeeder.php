<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Modules\Payroll\Models\PaymentItem;

class PaymentItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $paymentItem = new PaymentItem();
        $paymentItem->updateOrCreate([
            'id' => 1,
        ], [
            'title' => 'Basic Salary with Grade',
            'slug' => 'basic-salary',
            'type' => 'B',
            'frequency' => 12,
            'activated_at' => date('Y-m-d H:i:s')
        ]);
        $paymentItem->updateOrCreate([
            'id' => 2,
        ], [
            'title' => 'Provident Fund (SSF Office 10%)',
            'slug' => 'provident-fund',
            'type' => 'B',
            'frequency' => 12,
            'activated_at' => date('Y-m-d H:i:s'),
        ]);
        $paymentItem->updateOrCreate([
            'id' => 3,
        ], [
            'title' => 'Gratuity (SSF 8.33%)',
            'slug' => 'gratuity',
            'type' => 'B',
            'frequency' => 12,
            'activated_at' => date('Y-m-d H:i:s'),
        ]);
        $paymentItem->updateOrCreate([
            'id' => 4,
        ], [
            'title' => 'Medical Insurance (SSF 1.67%)',
            'slug' => 'medical-insurance',
            'type' => 'B',
            'frequency' => 12,
            'activated_at' => date('Y-m-d H:i:s'),
        ]);
        $paymentItem->updateOrCreate([
            'id' => 5,
        ], [
            'title' => 'Travel Allowance',
            'slug' => 'travel-allowance',
            'type' => 'B',
            'frequency' => 12,
            'activated_at' => date('Y-m-d H:i:s'),
        ]);
        $paymentItem->updateOrCreate([
            'id' => 6,
        ], [
            'title' => 'Remote Allowance',
            'slug' => 'remote-allowance',
            'type' => 'B',
            'frequency' => 12,
            'activated_at' => date('Y-m-d H:i:s'),
        ]);
        $paymentItem->updateOrCreate([
            'id' => 7,
        ], [
            'title' => 'Festival Allowance',
            'slug' => 'festival-allowance',
            'type' => 'B',
            'frequency' => 1,
            'activated_at' => date('Y-m-d H:i:s'),
        ]);

        $paymentItem->updateOrCreate([
            'id' => 8,
        ], [
            'title' => 'SSF (31%)',
            'slug' => 'ssf-deduction',
            'type' => 'D',
            'frequency' => 12,
            'activated_at' => date('Y-m-d H:i:s'),
        ]);
        $paymentItem->updateOrCreate([
            'id' => 9,
        ], [
            'title' => 'CIT',
            'slug' => 'cit',
            'type' => 'B',
            'frequency' => 12,
            'activated_at' => date('Y-m-d H:i:s'),
        ]);
        $paymentItem->updateOrCreate([
            'id' => 10,
        ], [
            'title' => 'Transition District Allowance',
            'slug' => 'transition-allowance',
            'type' => 'B',
            'frequency' => 12,
            'activated_at' => date('Y-m-d H:i:s'),
        ]);
    }
}
