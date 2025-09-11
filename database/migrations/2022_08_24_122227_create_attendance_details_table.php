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
        Schema::create('attendance_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('attendance_master_id');
            $table->date('attendance_date');
            $table->time('checkin')->nullable()->default(null);
            $table->time('checkout')->nullable()->default(null);
            $table->double('worked_hours', 4, 2)->nullable()->default(null);
            $table->double('charged_hours', 4, 2)->nullable()->default(null);
            $table->double('unrestricted_hours', 4, 2)->nullable()->default(null);
            $table->string('attendance_status')->nullable()->default(null);
            $table->unsignedBigInteger('created_by')->nullable()->default(null);
            $table->unsignedBigInteger('updated_by')->nullable()->default(null);
            $table->nullableTimestamps();

            $table->unique(['attendance_master_id', 'attendance_date']);
            $table->foreign('attendance_master_id')
                ->references('id')
                ->on('attendance_masters')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('attendance_details');
    }
};
