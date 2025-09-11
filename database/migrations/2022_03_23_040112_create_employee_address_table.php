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
        Schema::create('employee_address', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->unsignedBigInteger('permanent_province_id');
            $table->unsignedBigInteger('permanent_district_id');
            $table->unsignedBigInteger('permanent_local_level_id');
            $table->unsignedTinyInteger('permanent_ward');
            $table->string('permanent_tole')->nullable()->default(null);
            $table->unsignedBigInteger('temporary_province_id');
            $table->unsignedBigInteger('temporary_district_id');
            $table->unsignedBigInteger('temporary_local_level_id');
            $table->unsignedTinyInteger('temporary_ward');
            $table->string('temporary_tole')->nullable()->default(null);
            $table->unsignedBigInteger('created_by')->nullable()->default(null);
            $table->unsignedBigInteger('updated_by')->nullable()->default(null);
            $table->nullableTimestamps();

            $table->foreign('employee_id')->references('id')->on('employees');
            $table->foreign('permanent_province_id')->references('id')->on('lkup_provinces');
            $table->foreign('permanent_district_id')->references('id')->on('lkup_districts');
            $table->foreign('permanent_local_level_id')->references('id')->on('lkup_local_levels');
            $table->foreign('temporary_province_id')->references('id')->on('lkup_provinces');
            $table->foreign('temporary_district_id')->references('id')->on('lkup_districts');
            $table->foreign('temporary_local_level_id')->references('id')->on('lkup_local_levels');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('employee_address');
    }
};
