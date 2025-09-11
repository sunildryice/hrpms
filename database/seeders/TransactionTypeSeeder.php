<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Modules\Master\Models\TransactionType;

class TransactionTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $transactionType = new TransactionType();

        $transactionType->updateOrCreate([
            'title' => 'Fund Transferred'
        ]);

        $transactionType->updateOrCreate([
            'title' => 'Expense Settled'
        ]);
    }
}
