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
        Schema::create('trainings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('reviewer_id')->nullable()->default(null);
            $table->unsignedBigInteger('recommender_id')->nullable()->default(null);
            $table->unsignedBigInteger('approver_id')->nullable()->default(null);
            $table->unsignedBigInteger('activity_code_id')->nullable()->default(null);
            $table->unsignedBigInteger('account_code_id')->nullable()->default(null);
            $table->unsignedBigInteger('fiscal_year_id')->nullable()->default(null);
            $table->string('prefix')->nullable()->default(null);
            $table->unsignedInteger('training_number')->nullable()->default(null);
            $table->string('title')->nullable()->default(null);
            $table->date('start_date')->nullable()->default(null);
            $table->date('end_date')->nullable()->default(null);
            $table->unsignedDecimal('own_time')->nullable()->default(null);
            $table->unsignedDecimal('work_time')->nullable()->default(null);
            $table->string('duration')->nullable()->default(null);
            $table->decimal('course_fee', 12, 2)->nullable()->default(null);
            $table->text('description')->nullable()->default(null);
            $table->string('attachment')->nullable()->default(null);
            $table->unsignedBigInteger('status_id')->nullable()->default(null);
            $table->unsignedBigInteger('created_by')->nullable()->default(null);
            $table->unsignedBigInteger('updated_by')->nullable()->default(null);
            $table->nullableTimestamps();

            $table->foreign('activity_code_id')->references('id')->on('lkup_activity_codes');
            $table->foreign('account_code_id')->references('id')->on('lkup_account_codes');
            $table->foreign('reviewer_id')->references('id')->on('users');
            $table->foreign('recommender_id')->references('id')->on('users');
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
        Schema::dropIfExists('trainings');
    }
};
