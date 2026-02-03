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
        Schema::dropIfExists('work_plan_logs');
        Schema::dropIfExists('work_plan_daily_logs');
        Schema::dropIfExists('work_plans');

        Schema::create('work_plan', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->date('from_date');
            $table->date('to_date');
            $table->nullableTimestamps();

            $table->foreign('employee_id')->references('id')->on('employees');
        });

        Schema::create('work_plan_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('work_plan_id');
            $table->unsignedBigInteger('project_id');
            $table->unsignedBigInteger('project_activity_id');
            $table->text('plan_tasks')->nullable();
            $table->enum('status', ['not_started', 'under_progress', 'no_required', 'completed'])->nullable();
            $table->nullableTimestamps();

            $table->foreign('work_plan_id')->references('id')->on('work_plan');
            $table->foreign('project_id')->references('id')->on('projects');
            $table->foreign('project_activity_id')->references('id')->on('project_activities');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('work_plan_details');
        Schema::dropIfExists('work_plan');
    }
};
