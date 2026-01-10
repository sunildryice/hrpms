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
        Schema::create('lkup_activity_stages', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('description')->nullable();
            $table->dateTime('activated_at')->nullable()->default(null);
            $table->unsignedBigInteger('created_by')->nullable()->default(null);
            $table->unsignedBigInteger('updated_by')->nullable()->default(null);
            $table->nullableTimestamps();
        });

        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable()->default(null);
            $table->date('start_date')->nullable();
            $table->date('completion_date')->nullable();
            $table->unsignedBigInteger('team_lead_id')->nullable()->default(null);
            $table->unsignedBigInteger('focal_person_id')->nullable()->default(null);
            $table->unsignedBigInteger('created_by')->nullable()->default(null);
            $table->unsignedBigInteger('updated_by')->nullable()->default(null);
            $table->nullableTimestamps();

            $table->foreign('team_lead_id')->references('id')->on('users');
            $table->foreign('focal_person_id')->references('id')->on('users');
        });

        Schema::create('project_members', function (Blueprint $table) {
            $table->unsignedBigInteger('project_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();

            $table->foreign('project_id')->references('id')->on('projects');
            $table->foreign('user_id')->references('id')->on('users');
            $table->unique(['project_id', 'user_id']);
        });

        Schema::create('project_activity_stages', function (Blueprint $table) {
            $table->unsignedBigInteger('project_id')->nullable();
            $table->unsignedBigInteger('activity_stage_id')->nullable();

            $table->foreign('project_id')->references('id')->on('projects');
            $table->foreign('activity_stage_id')->references('id')->on('lkup_activity_stages');
            $table->unique(['project_id', 'activity_stage_id']);
        });

        Schema::create('project_activities', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_id');
            $table->unsignedBigInteger('activity_stage_id');
            $table->enum('activity_level', ['theme', 'activity', 'sub_activity']);
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->string('title');
            $table->text('deliverables')->nullable();
            $table->text('budget_description')->nullable();

            $table->date('start_date')->nullable();
            $table->date('completion_date')->nullable();
            $table->date('actual_start_date')->nullable();
            $table->date('actual_completion_date')->nullable();

            $table->enum('status', ['not_started', 'under_progress', 'no_required', 'completed'])->nullable();
            $table->unsignedBigInteger('created_by')->nullable()->default(null);
            $table->unsignedBigInteger('updated_by')->nullable()->default(null);
            $table->nullableTimestamps();

            $table->foreign('project_id')->references('id')->on('projects');
            $table->foreign('activity_stage_id')->references('id')->on('lkup_activity_stages');
            $table->foreign('parent_id')->references('id')->on('project_activities');
        });

        Schema::create('project_activity_members', function (Blueprint $table) {
            $table->unsignedBigInteger('activity_id');
            $table->unsignedBigInteger('user_id');

            $table->foreign('activity_id')->references('id')->on('project_activities');
            $table->foreign('user_id')->references('id')->on('users');
            $table->unique(['activity_id', 'user_id']);
        });

        Schema::create('project_activity_extensions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_id');
            $table->unsignedBigInteger('activity_id');
            $table->date('extended_completion_date')->nullable();
            $table->text('reason')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->nullableTimestamps();

            $table->foreign('project_id')->references('id')->on('projects');
            $table->foreign('activity_id')->references('id')->on('project_activities');
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
        Schema::dropIfExists('project_activity_extensions');
        Schema::dropIfExists('project_activity_members');
        Schema::dropIfExists('project_activities');
        Schema::dropIfExists('project_activity_stages');
        Schema::dropIfExists('project_members');
        Schema::dropIfExists('projects');
        Schema::dropIfExists('lkup_activity_stages');
    }
};
