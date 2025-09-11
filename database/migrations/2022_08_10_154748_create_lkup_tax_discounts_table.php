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
        Schema::create('lkup_tax_discounts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('payroll_fiscal_year_id');
            $table->string('title');
            $table->string('slug');
            $table->decimal('discount_amount_to', 10, 0)->default(0);
            $table->nullableTimestamps();

            $table->unique(['payroll_fiscal_year_id', 'slug']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lkup_tax_discounts');
    }
};
