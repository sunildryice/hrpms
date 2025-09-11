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
        Schema::create('employee_leaves', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->unsignedBigInteger('fiscal_year_id');
            $table->unsignedBigInteger('leave_type_id');
            $table->date('reported_date')->nullable()->default(null);
            $table->double('opening_balance', 6,2)->nullable()->default(null);
            $table->double('earned', 6,2)->nullable()->default(null);
            $table->double('taken', 6,2)->nullable()->default(null);
            $table->double('current_month_taken', 6,2)->nullable()->default(null);
            $table->double('lapsed', 6,2)->nullable()->default(null);
            $table->double('balance', 6,2)->nullable()->default(null);
            $table->text('remarks')->nullable()->default(null);
            $table->unsignedBigInteger('created_by')->nullable()->default(null);
            $table->unsignedBigInteger('updated_by')->nullable()->default(null);
            $table->nullableTimestamps();

            $table->foreign('employee_id')->references('id')->on('employees');
            $table->foreign('fiscal_year_id')->references('id')->on('lkup_fiscal_years');
            $table->foreign('leave_type_id')->references('id')->on('lkup_leave_types');
            $table->unique(['employee_id', 'fiscal_year_id', 'leave_type_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('employee_leaves');
    }
};
