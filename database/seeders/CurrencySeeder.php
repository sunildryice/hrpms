<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Modules\Master\Models\Currency;

class CurrencySeeder extends Seeder
{
    /**
     * Run the database seeder
     * @return void
     */
    public function run()
    {
        $currency = new Currency();

        $currency->create([
            'title' => 'NPR'
        ]);

        $currency->create([
            'title' => 'USD'
        ]);
    }
}