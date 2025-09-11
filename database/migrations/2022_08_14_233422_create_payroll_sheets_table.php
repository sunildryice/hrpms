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
        Schema::create('payroll_sheets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('payroll_batch_id');
            $table->unsignedBigInteger('employee_id');
            $table->unsignedBigInteger('department_id')->nullable()->default(null);
            $table->unsignedBigInteger('designation_id')->nullable()->default(null);
            $table->date('start_date')->nullable()->default(null);
            $table->date('end_date')->nullable()->default(null);
            $table->boolean('married')->default(0);
            $table->boolean('disabled')->default(0);
            $table->string('remote_category')->nullable()->default(null);
            $table->decimal('gross_amount', 12, 2)->default(0);
            $table->decimal('total_deduction_amount', 12,2)->default(0);
            $table->decimal('sst_amount',12,2)->default(0);
            $table->decimal('tax_liability',12,2)->default(0);
            $table->decimal('tax_discount_amount',12,2)->default(0);
            $table->decimal('tax_amount',12,2)->default(0);
            $table->decimal('tds_amount',12,2)->default(0);
            $table->decimal('net_amount',12,2)->default(0);
            $table->unsignedBigInteger('created_by')->nullable()->default(null);
            $table->unsignedBigInteger('updated_by')->nullable()->default(null);
            $table->nullableTimestamps();

            $table->foreign('payroll_batch_id', 'payroll_sheet_batch_id_foreign')->references('id')->on('payroll_batches');
            $table->foreign('employee_id')->references('id')->on('employees');
            $table->foreign('department_id')->references('id')->on('lkup_departments');
            $table->foreign('designation_id')->references('id')->on('lkup_designations');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payroll_sheets');
    }
};
