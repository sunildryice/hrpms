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
        Schema::create('work_plan_daily_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('work_plan_id');
            $table->unsignedBigInteger('activity_area_id')->nullable()->default(null);
            $table->unsignedBigInteger('priority_id')->nullable()->default(null);
            $table->date('log_date');
            $table->text('major_activities')->nullable()->default(null);
            $table->string('status')->nullable()->default(null);
            $table->text('other_activities')->nullable()->default(null);
            $table->text('remarks')->nullable()->default(null);
            $table->nullableTimestamps();

            $table->foreign('work_plan_id')->references('id')->on('work_plans');
            $table->unique(['work_plan_id', 'log_date']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('work_plan_daily_logs');
    }
};
