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
        Schema::create('event_completions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('district_id')->nullable()->default(null);
            $table->unsignedBigInteger('office_id')->nullable()->default(null);
            $table->unsignedBigInteger('activity_code_id');
            $table->unsignedBigInteger('donor_code_id')->nullable()->default(null);
            $table->unsignedBigInteger('project_code_id')->nullable()->default(null);
            $table->unsignedBigInteger('account_code_id')->nullable()->default(null);
            $table->string('venue')->nullable()->default(null);
            $table->dateTime('program_date')->nullable()->default(null);
            $table->text('background')->nullable()->default(null);
            $table->text('objectives')->nullable()->default(null);
            $table->text('process')->nullable()->default(null);
            $table->text('closing')->nullable()->default(null);
            $table->unsignedBigInteger('requester_id');
            $table->unsignedBigInteger('reviewer_id')->nullable()->default(null);
            $table->unsignedBigInteger('approver_id')->nullable()->default(null);
            $table->unsignedBigInteger('status_id')->nullable()->default(null);
            $table->unsignedBigInteger('created_by')->nullable()->default(null);
            $table->unsignedBigInteger('updated_by')->nullable()->default(null);
            $table->nullableTimestamps();
            $table->foreign('requester_id')->references('id')->on('users');
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('reviewer_id')->references('id')->on('users');
            $table->foreign('approver_id')->references('id')->on('users');
            $table->foreign('activity_code_id')->references('id')->on('lkup_activity_codes');
            $table->foreign('donor_code_id')->references('id')->on('lkup_donor_codes');
            $table->foreign('project_code_id')->references('id')->on('lkup_project_codes');
            $table->foreign('account_code_id')->references('id')->on('lkup_account_codes');
            $table->foreign('office_id')->references('id')->on('lkup_offices');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('event_completions');
    }
};
