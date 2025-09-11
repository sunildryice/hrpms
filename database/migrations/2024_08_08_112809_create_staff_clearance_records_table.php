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
        Schema::create('staff_clearance_records', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('staff_clearance_id');
            $table->unsignedBigInteger('clearance_department_id');
            $table->unsignedBigInteger('employee_id')->nullable();
            $table->dateTime('cleared_at')->nullable();
            $table->text('remarks')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->nullableTimestamps();

            $table->foreign('staff_clearance_id')->references('id')->on('exit_staff_clearances');
            $table->foreign('clearance_department_id')->references('id')->on('lkup_staff_clearance_departments');
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('staff_clearance_records');
    }
};
