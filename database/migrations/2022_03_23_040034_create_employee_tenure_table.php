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
        Schema::create('employee_tenures', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->unsignedBigInteger('designation_id');
            $table->unsignedBigInteger('department_id');
            $table->unsignedBigInteger('supervisor_id')->nullable()->default(null);
            $table->unsignedBigInteger('cross_supervisor_id')->nullable()->default(null);
            $table->unsignedBigInteger('next_line_manager_id')->nullable()->default(null);
            $table->unsignedBigInteger('office_id')->nullable()->default(null);
            $table->unsignedBigInteger('duty_station_id')->nullable()->default(null);
            $table->date('joined_date')->nullable()->default(null);
            $table->date('to_date')->nullable()->default(null);
            $table->string('remarks')->nullable()->default(null);
            $table->unsignedBigInteger('created_by')->nullable()->default(null);
            $table->unsignedBigInteger('updated_by')->nullable()->default(null);
            $table->nullableTimestamps();

            $table->foreign('employee_id')->references('id')->on('employees');
            $table->foreign('designation_id')->references('id')->on('lkup_designations');
            $table->foreign('department_id')->references('id')->on('lkup_departments');
            $table->foreign('supervisor_id')->references('id')->on('employees');
            $table->foreign('cross_supervisor_id')->references('id')->on('employees');
            $table->foreign('next_line_manager_id')->references('id')->on('employees');
            $table->foreign('office_id')->references('id')->on('lkup_offices');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('employee_tenures');
    }
};
