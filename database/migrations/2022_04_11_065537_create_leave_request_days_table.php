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
        Schema::create('leave_request_days', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('leave_request_id');
            $table->date('leave_date');
            $table->unsignedBigInteger('leave_mode_id')->nullable()->default(null);
            $table->unsignedTinyInteger('leave_duration')->nullable()->default(null);
            $table->text('leave_remarks')->nullable()->default(null);
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by')->nullable()->default(null);
            $table->nullableTimestamps();

            $table->foreign('leave_request_id')->references('id')->on('leave_requests');
            $table->foreign('leave_mode_id')->references('id')->on('lkup_leave_modes');
            $table->foreign('created_by')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('leave_request_days');
    }
};
