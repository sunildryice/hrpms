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
        Schema::create('employee_requisitions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('fiscal_year_id')->nullable()->default(null);
            $table->unsignedBigInteger('employee_type_id')->nullable()->default(null);
            $table->unsignedBigInteger('office_id')->nullable()->default(null);
            $table->unsignedBigInteger('duty_station_id')->nullable()->default(null);
            $table->unsignedBigInteger('activity_code_id')->nullable()->default(null);
            $table->unsignedBigInteger('account_code_id')->nullable()->default(null);
            $table->unsignedBigInteger('donor_code_id')->nullable()->default(null);
            $table->string('position_title')->nullable()->default(null);
            $table->string('position_level')->nullable()->default(null);
            $table->string('replacement_for')->nullable()->default(null);
            $table->date('requested_date')->nullable()->default(null);
            $table->date('required_date')->nullable()->default(null);
            $table->boolean('budgeted')->default(0);
            $table->unsignedTinyInteger('work_load')->nullable()->default(null);
            $table->string('duration')->nullable()->default(null);
            $table->longText('reason_for_request')->nullable()->default(null);
            $table->string('employee_type_other')->nullable()->default(null);
            $table->string('education_required')->nullable()->default(null);
            $table->string('education_preferred')->nullable()->default(null);
            $table->string('experience_required')->nullable()->default(null);
            $table->string('experience_preferred')->nullable()->default(null);
            $table->string('skills_required')->nullable()->default(null);
            $table->string('skills_preferred')->nullable()->default(null);
            $table->string('other_required')->nullable()->default(null);
            $table->string('other_preferred')->nullable()->default(null);
            $table->string('logistics_requirement')->nullable()->default(null);
            $table->boolean('tor_jd_submitted')->nullable()->default(0);
            $table->date('tentative_submission_date')->nullable()->default(null);
            $table->string('tor_jd_attachment')->nullable()->default(null);
            $table->unsignedBigInteger('reviewer_id')->nullable()->default(null);
            $table->unsignedBigInteger('approver_id')->nullable()->default(null);
            $table->unsignedBigInteger('status_id')->nullable()->default(null);
            $table->unsignedBigInteger('created_by')->nullable()->default(null);
            $table->unsignedBigInteger('updated_by')->nullable()->default(null);
            $table->nullableTimestamps();

            $table->foreign('activity_code_id')->references('id')->on('lkup_activity_codes');
            $table->foreign('account_code_id')->references('id')->on('lkup_account_codes');
            $table->foreign('donor_code_id')->references('id')->on('lkup_donor_codes');
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('reviewer_id')->references('id')->on('users');
            $table->foreign('approver_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('employee_requisitions');
    }
};
