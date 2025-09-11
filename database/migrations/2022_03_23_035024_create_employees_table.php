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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('office_id')->nullable()->default(null);
            $table->unsignedBigInteger('designation_id')->nullable()->default(null);
            $table->unsignedBigInteger('department_id')->nullable()->default(null);
            $table->unsignedBigInteger('employee_type_id')->nullable()->default(null);
            $table->unsignedInteger('employee_code')->unique();
            $table->string('full_name');
            $table->string('official_email_address')->nullable()->default(null);
            $table->string('personal_email_address')->nullable()->default(null);
            $table->string('telephone_number', 20)->nullable()->default(null);
            $table->string('mobile_number', 20)->nullable()->default(null);
            $table->unsignedBigInteger('marital_status')->nullable()->default(null);
            $table->unsignedBigInteger('gender')->nullable()->default(null);
            $table->string('citizenship_number')->nullable()->default(null);
            $table->string('pan_number', 9)->nullable()->default(null);
            $table->string('citizenship_attachment')->nullable()->default(null);
            $table->string('pan_attachment')->nullable()->default(null);
            $table->string('signature')->nullable()->default(null);
            $table->string('profile_picture')->nullable()->default(null);
            $table->date('date_of_birth')->nullable()->default(null);
            $table->date('joined_date')->nullable()->default(null);
            $table->date('probation_complete_date')->nullable()->default(null);
            $table->date('last_working_date')->nullable()->default(null);
            $table->unsignedBigInteger('religion_id')->nullable()->default(null);
            $table->unsignedBigInteger('caste_id')->nullable()->default(null);
            $table->unsignedBigInteger('created_by')->nullable()->default(null);
            $table->unsignedBigInteger('updated_by')->nullable()->default(null);
            $table->dateTime('activated_at')->nullable()->default(null);
            $table->nullableTimestamps();

            $table->foreign('office_id')->references('id')->on('lkup_offices');
            $table->foreign('designation_id')->references('id')->on('lkup_designations');
            $table->foreign('department_id')->references('id')->on('lkup_departments');
            $table->foreign('employee_type_id')->references('id')->on('lkup_employee_types');
            $table->foreign('religion_id')->references('id')->on('lkup_religions');
            $table->foreign('caste_id')->references('id')->on('lkup_castes');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('employee_id')->after('id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('employee_id');
        });
        Schema::dropIfExists('employees');
    }
};
