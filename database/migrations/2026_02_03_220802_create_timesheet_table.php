<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('timesheets', function (Blueprint $table) {
            $table->id();
            $table->year('year');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->unsignedTinyInteger('month');
            $table->string('month_name')->nullable();
            $table->unsignedBigInteger('status_id')->nullable()->default(null);
            $table->unsignedBigInteger('approver_id')->nullable()->default(null);
            $table->unsignedBigInteger('requester_id')->nullable()->default(null);
            $table->unsignedBigInteger('updated_by')->nullable()->default(null);
            $table->nullableTimestamps();

            $table->foreign('requester_id')->references('id')->on('users');
            $table->foreign('approver_id')->references('id')->on('users');
        });

        Schema::create('timesheet_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('timesheet_id');
            $table->unsignedBigInteger('user_id');
            $table->text('log_remarks')->nullable()->default(null);
            $table->unsignedBigInteger('status_id');
            $table->nullableTimestamps();

            $table->foreign('timesheet_id')->references('id')->on('timesheets');
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('timesheet_logs');
        Schema::dropIfExists('timesheets');
    }
};
