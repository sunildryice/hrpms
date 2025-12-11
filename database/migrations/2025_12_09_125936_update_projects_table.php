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
//        Schema::table('lkup_project_codes', function (Blueprint $table) {
//            $table->date('start_date')->nullable();
//            $table->date('end_date')->nullable();
//        });
//
//        Schema::table('lkup_activity_areas', function (Blueprint $table) {
//            $table->unsignedBigInteger('project_id')->nullable();
//
//            $table->foreign('project_id')->references('id')->on('lkup_project_codes');
//        });

        Schema::create('lkup_activity_stages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_id');
            $table->string('title');
            $table->string('description')->nullable();
            $table->dateTime('activated_at')->nullable()->default(null);
            $table->unsignedBigInteger('created_by')->nullable()->default(null);
            $table->unsignedBigInteger('updated_by')->nullable()->default(null);
            $table->nullableTimestamps();

            $table->foreign('project_id')->references('id')->on('lkup_project_codes');
        });

        Schema::create('lkup_activities', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_id');
            $table->unsignedBigInteger('stage_id');
            $table->string('title');
            $table->string('deliverables')->nullable();
            $table->date('submission_date')->nullable();
            $table->date('completion_date')->nullable();
            $table->text('budget_description')->nullable();
            $table->string('status')->nullable();
            $table->unsignedBigInteger('created_by')->nullable()->default(null);
            $table->unsignedBigInteger('updated_by')->nullable()->default(null);
            $table->nullableTimestamps();

            $table->foreign('project_id')->references('id')->on('lkup_project_codes');
            $table->foreign('stage_id')->references('id')->on('lkup_activity_stages');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
//        Schema::table('lkup_project_codes', function (Blueprint $table) {
//            $table->dropColumn(['start_date', 'end_date']);
//        });
//        Schema::table('lkup_activity_areas', function (Blueprint $table) {
//            $table->dropColumn(['project_id']);
//        });
    }
};
