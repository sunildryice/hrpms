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
        Schema::create('employee_leave_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_leave_id');
            $table->unsignedBigInteger('fiscal_year_id');
            $table->unsignedTinyInteger('month')->nullable()->default(null);
            $table->nullableTimestamps();

            $table->foreign('fiscal_year_id')->references('id')->on('lkup_fiscal_years');
            $table->foreign('employee_leave_id')->references('id')->on('employee_leaves');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('employee_leave_logs');
    }
};
