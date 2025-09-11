<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Modules\Payroll\Models\TaxDiscount;
use Modules\Payroll\Models\TaxRate;

class TaxRateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $taxRate = new TaxRate();
        $taxRate->create([
            'id'=>1,
            'payroll_fiscal_year_id'=>1,
            'married' => 0,
            'annual_income_from' => 1,
            'annual_income_to' => 500000,
            'tax_rate' => 1,
        ]);
        $taxRate->create([
            'id'=>2,
            'payroll_fiscal_year_id'=>1,
            'married' => 0,
            'annual_income_from' => 500001,
            'annual_income_to' => 700000,
            'tax_rate' => 10,
        ]);
        $taxRate->create([
            'id'=>3,
            'payroll_fiscal_year_id'=>1,
            'married' => 0,
            'annual_income_from' => 700001,
            'annual_income_to' => 1000000,
            'tax_rate' => 20,
        ]);
        $taxRate->create([
            'id'=>4,
            'payroll_fiscal_year_id'=>1,
            'married' => 0,
            'annual_income_from' => 1000001,
            'annual_income_to' => 2000000,
            'tax_rate' => 30,
        ]);
        $taxRate->create([
            'id'=>5,
            'payroll_fiscal_year_id'=>1,
            'married' => 0,
            'annual_income_from' => 2000001,
            'annual_income_to' => 9999999999,
            'tax_rate' => 36,
        ]);

        $taxRate->create([
            'id'=>6,
            'payroll_fiscal_year_id'=>1,
            'married' => 1,
            'annual_income_from' => 1,
            'annual_income_to' => 600000,
            'tax_rate' => 1,
        ]);
        $taxRate->create([
            'id'=>7,
            'payroll_fiscal_year_id'=>1,
            'married' => 1,
            'annual_income_from' => 600001,
            'annual_income_to' => 800000,
            'tax_rate' => 10,
        ]);
        $taxRate->create([
            'id'=>8,
            'payroll_fiscal_year_id'=>1,
            'married' => 1,
            'annual_income_from' => 800001,
            'annual_income_to' => 1100000,
            'tax_rate' => 20,
        ]);
        $taxRate->create([
            'id'=>9,
            'payroll_fiscal_year_id'=>1,
            'married' => 1,
            'annual_income_from' => 1100001,
            'annual_income_to' => 2000000,
            'tax_rate' => 30,
        ]);
        $taxRate->create([
            'id'=>10,
            'payroll_fiscal_year_id'=>1,
            'married' => 1,
            'annual_income_from' => 2000001,
            'annual_income_to' => 9999999999,
            'tax_rate' => 36,
        ]);


        $taxDiscount = new TaxDiscount();
        $taxDiscount->create([
            'id'=>1,
            'payroll_fiscal_year_id'=>1,
            'title' => 'Category A',
            'slug' => 'category-a',
            'discount_amount_to' => 50000,
        ]);
        $taxDiscount->create([
            'id'=>2,
            'payroll_fiscal_year_id'=>1,
            'title' => 'Category B',
            'slug' => 'category-b',
            'discount_amount_to' => 40000,
        ]);
        $taxDiscount->create([
            'id'=>3,
            'payroll_fiscal_year_id'=>1,
            'title' => 'Category C',
            'slug' => 'category-c',
            'discount_amount_to' => 30000,
        ]);
        $taxDiscount->create([
            'id'=>4,
            'payroll_fiscal_year_id'=>1,
            'title' => 'Category D',
            'slug' => 'category-d',
            'discount_amount_to' => 20000,
        ]);
        $taxDiscount->create([
            'id'=>5,
            'payroll_fiscal_year_id'=>1,
            'title' => 'Category E',
            'slug' => 'category-e',
            'discount_amount_to' => 10000,
        ]);
    }
}
