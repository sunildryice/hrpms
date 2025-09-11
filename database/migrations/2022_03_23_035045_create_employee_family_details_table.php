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
        Schema::create('employee_family_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->unsignedBigInteger('family_relation_id');
            $table->string('full_name');
            $table->date('date_of_birth')->nullable()->default(null);
            $table->dateTime('emergency_contact_at')->nullable()->default(null);
            $table->dateTime('nominee_at')->nullable()->default(null);
            $table->unsignedBigInteger('province_id')->nullable()->default(null);
            $table->unsignedBigInteger('district_id')->nullable()->default(null);
            $table->unsignedBigInteger('local_level_id')->nullable()->default(null);
            $table->unsignedTinyInteger('ward')->nullable()->default(null);
            $table->string('tole')->nullable()->default(null);
            $table->text('remarks')->nullable()->default(null);
            $table->string('contact_number', 20)->nullable()->default(null);
            $table->unsignedBigInteger('created_by')->nullable()->default(null);
            $table->unsignedBigInteger('updated_by')->nullable()->default(null);
            $table->nullableTimestamps();

            $table->foreign('employee_id')->references('id')->on('employees');
            $table->foreign('province_id')->references('id')->on('lkup_provinces');
            $table->foreign('district_id')->references('id')->on('lkup_districts');
            $table->foreign('local_level_id')->references('id')->on('lkup_local_levels');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('employee_family_details');
    }
};
