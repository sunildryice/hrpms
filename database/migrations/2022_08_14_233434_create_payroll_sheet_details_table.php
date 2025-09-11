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
        Schema::create('payroll_sheet_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('payroll_sheet_id');
            $table->unsignedBigInteger('payment_item_id');
            $table->decimal('amount',12,2)->default(0);
            $table->nullableTimestamps();

            $table->foreign('payroll_sheet_id', 'payroll_sheet_detail_master_foreign')->references('id')->on('payroll_sheets');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payroll_sheet_details');
    }
};
