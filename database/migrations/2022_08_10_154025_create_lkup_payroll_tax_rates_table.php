<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lkup_payroll_tax_rates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('payroll_fiscal_year_id');
            $table->boolean('married')->default(0);
            $table->decimal('annual_income_from', 13, 0)->nullable()->default(null);
            $table->decimal('annual_income_to', 13, 0)->nullable()->default(null);
            $table->decimal('tax_rate', 4, 2)->nullable()->default(null);
            $table->nullableTimestamps();

            $table->foreign('payroll_fiscal_year_id')->references('id')->on('lkup_payroll_fiscal_years');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lkup_payroll_tax_rates');
    }
};
