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
        Schema::create('payroll_batches', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('payroll_fiscal_year_id');
            $table->unsignedBigInteger('fiscal_year_id')->nullable()->default(null);
            $table->unsignedBigInteger('employee_type_id')->nullable()->default(null);
            $table->unsignedTinyInteger('month')->nullable()->default(null);
            $table->date('posted_date')->nullable()->default(null);
            $table->date('approved_date')->nullable()->default(null);
            $table->text('description')->nullable()->default(null);
            $table->unsignedBigInteger('status_id')->nullable()->default(null);
            $table->unsignedBigInteger('reviewer_id')->nullable()->default(null);
            $table->unsignedBigInteger('approver_id')->nullable()->default(null);
            $table->unsignedBigInteger('created_by')->nullable()->default(null);
            $table->unsignedBigInteger('updated_by')->nullable()->default(null);
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
        Schema::dropIfExists('payroll_batches');
    }
};
