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
        Schema::create('off_day_works', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->unsignedBigInteger('requester_id');
            $table->unsignedBigInteger('approver_id');
            $table->unsignedBigInteger('project_id');
            $table->unsignedBigInteger('fiscal_year_id');
            $table->text('reason');
            $table->json('deliverables');
            $table->unsignedBigInteger('status_id');


            $table->date('request_date')->nullable()->default(null);
            $table->unsignedBigInteger('office_id')->nullable()->default(null);
            $table->unsignedBigInteger('department_id')->nullable()->default(null);
            $table->unsignedBigInteger('created_by')->nullable()->default(null);
            $table->unsignedBigInteger('updated_by')->nullable()->default(null);
            $table->timestamps();


            $table->foreign('fiscal_year_id')->references('id')->on('lkup_fiscal_years');
            $table->foreign('requester_id')->references('id')->on('users');
            $table->foreign('approver_id')->references('id')->on('users');
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');
            $table->foreign('project_id')->references('id')->on('lkup_project_codes');
            $table->foreign('office_id')->references('id')->on('lkup_offices');
            $table->foreign('department_id')->references('id')->on('lkup_departments');
        });

        Schema::create('off_day_work_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('off_day_work_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('original_user_id')->nullable()->default(null);
            $table->text('log_remarks')->nullable()->default(null);
            $table->unsignedBigInteger('status_id');
            $table->nullableTimestamps();

            $table->foreign('off_day_work_id')->references('id')->on('off_day_works');
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('original_user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

        Schema::dropIfExists('off_day_work_logs');
        Schema::dropIfExists('off_day_works');
    }
};
