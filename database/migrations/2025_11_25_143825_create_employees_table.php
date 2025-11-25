<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('office_id')->nullable();
            $table->unsignedBigInteger('designation_id')->nullable();
            $table->unsignedBigInteger('department_id')->nullable();
            $table->unsignedBigInteger('employee_type_id')->nullable();
            $table->unsignedInteger('employee_code')->unique();
            $table->string('full_name');
            $table->string('official_email_address')->nullable();
            $table->string('personal_email_address')->nullable();
            $table->string('mobile_number', 20)->nullable();
            $table->unsignedBigInteger('marital_status')->nullable();
            $table->unsignedBigInteger('gender')->nullable();
            $table->string('citizenship_number')->nullable();
            $table->string('pan_number', 9)->nullable();
            $table->string('nid_number')->nullable();
            $table->string('passport_number')->nullable();
            $table->string('passport_attachment')->nullable(); 
            $table->string('vehicle_license_number')->nullable();
            $table->json('vehicle_license_category')->nullable(); 
            $table->string('citizenship_attachment')->nullable();
            $table->string('pan_attachment')->nullable();
            $table->string('signature')->nullable();
            $table->string('profile_picture')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->date('joined_date')->nullable();
            $table->date('probation_complete_date')->nullable();
            $table->date('last_working_date')->nullable();
            $table->unsignedBigInteger('religion_id')->nullable();
            $table->unsignedBigInteger('caste_id')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->dateTime('activated_at')->nullable();
            $table->nullableTimestamps();

            $table->foreign('office_id')->references('id')->on('lkup_offices')->onDelete('set null');
            $table->foreign('designation_id')->references('id')->on('lkup_designations')->onDelete('set null');
            $table->foreign('department_id')->references('id')->on('lkup_departments')->onDelete('set null');
            $table->foreign('employee_type_id')->references('id')->on('lkup_employee_types')->onDelete('set null');
            $table->foreign('religion_id')->references('id')->on('lkup_religions')->onDelete('set null');
            $table->foreign('caste_id')->references('id')->on('lkup_castes')->onDelete('set null');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};