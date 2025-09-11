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
        Schema::create('probationary_reviews', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('review_id');
            $table->unsignedBigInteger('employee_id');
            $table->date('date')->nullable()->default(null);
            $table->text('remarks')->nullable()->default(null);

            $table->text('performance_improvements')->nullable()->default(null);
            $table->text('concern_address_summary')->nullable()->default(null);
            $table->text('employee_performance_progress')->nullable()->default(null);
            $table->boolean('objectives_met')->default(0);
            $table->text('objectives_review_remarks')->nullable()->default(null);
            $table->date('objectives_review_date')->nullable()->default(null);
            $table->boolean('development_addressed')->default(0);
            $table->text('development_review_remarks')->nullable()->default(null);
            $table->date('development_review_date')->nullable()->default(null);
            $table->text('supervisor_recommendation')->nullable()->default(null);
            $table->text('director_recommendation')->nullable()->default(null);
            $table->boolean('appointment_confirmed')->default(0);
            $table->text('reason_to_address_difficulty')->nullable()->default(null);
            $table->text('employee_remarks')->nullable()->default(null);
            $table->boolean('probation_extended')->default(0);
            $table->text('reason_and_improvement_to_extend')->nullable()->default(null);
            $table->date('next_probation_complete_date')->nullable()->default(null);
            $table->unsignedTinyInteger('extension_length')->nullable()->default(null)->comment('In Month');

            $table->unsignedBigInteger('reviewer_id')->nullable()->default(null);
            $table->unsignedBigInteger('approver_id')->nullable()->default(null);
            $table->unsignedBigInteger('status_id');
            $table->unsignedBigInteger('created_by')->nullable()->default(null);
            $table->unsignedBigInteger('updated_by')->nullable()->default(null);
            $table->nullableTimestamps();

            $table->foreign('review_id')->references('id')->on('lkup_probationary_review_types');
            $table->foreign('employee_id')->references('id')->on('employees');
            $table->foreign('reviewer_id')->references('id')->on('users');
            $table->foreign('approver_id')->references('id')->on('users');
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
        Schema::dropIfExists('probationary_reviews');
    }
};
