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
        Schema::create('employee_exit_payables', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->date('salary_date_from')->nullable()->default(null);
            $table->date('salary_date_to')->nullable()->default(null);
            $table->unsignedMediumInteger('leave_balance')->nullable()->default(null);
            $table->decimal('salary_amount', 12 ,2)->nullable()->default(null);
            $table->decimal('festival_bonus', 12 ,2)->nullable()->default(null);
            $table->decimal('gratuity_amount', 12 ,2)->nullable()->default(null);
            $table->decimal('other_amount', 12 ,2)->nullable()->default(null);
            $table->decimal('advance_amount', 12 ,2)->nullable()->default(null);
            $table->decimal('loan_amount', 12 ,2)->nullable()->default(null);
            $table->decimal('other_payable_amount', 12 ,2)->nullable()->default(null);
            $table->unsignedBigInteger('reviewer_id')->nullable()->default(null);
            $table->unsignedBigInteger('approver_id')->nullable()->default(null);
            $table->unsignedBigInteger('status_id')->nullable()->default(null);
            $table->unsignedBigInteger('created_by')->nullable()->default(null);
            $table->unsignedBigInteger('updated_by')->nullable()->default(null);
            $table->nullableTimestamps();

            $table->foreign('employee_id')->references('id')->on('employees');
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('reviewer_id')->references('id')->on('users');
            $table->foreign('approver_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('employee_exit_payables');
    }
};
