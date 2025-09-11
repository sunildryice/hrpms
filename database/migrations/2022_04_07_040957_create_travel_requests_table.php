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
        Schema::create('travel_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('office_id')->nullable()->default(null);
            $table->unsignedBigInteger('department_id')->nullable()->default(null);
            $table->unsignedBigInteger('travel_type_id');
            $table->unsignedBigInteger('project_code_id')->nullable()->default(null);
            $table->unsignedBigInteger('requester_id');
            $table->unsignedBigInteger('reviewer_id')->nullable()->default(null);
            $table->unsignedBigInteger('approver_id')->nullable()->default(null);
            $table->unsignedBigInteger('fiscal_year_id')->nullable()->default(null);
            $table->string('prefix')->nullable()->default(null);
            $table->unsignedInteger('travel_number')->nullable()->default(null);
            $table->unsignedInteger('modification_number')->nullable()->default(null);
            $table->unsignedBigInteger('modification_travel_request_id')->nullable()->default(null);
            $table->date('departure_date');
            $table->date('return_date');
            $table->date('request_date');
            $table->string('final_destination')->nullable()->default(null);
            $table->text('purpose_of_travel')->nullable()->default(null);
            $table->text('remarks')->nullable()->default(null);
            $table->unsignedBigInteger('status_id')->nullable()->default(null);
            $table->unsignedBigInteger('substitute_id')->nullable()->default(null);
            $table->unsignedBigInteger('created_by')->nullable()->default(null);
            $table->unsignedBigInteger('updated_by')->nullable()->default(null);
            $table->nullableTimestamps();

            $table->foreign('office_id')->references('id')->on('lkup_offices');
            $table->foreign('department_id')->references('id')->on('lkup_departments');
            $table->foreign('project_code_id')->references('id')->on('lkup_project_codes');
            $table->foreign('fiscal_year_id')->references('id')->on('lkup_fiscal_years');
            $table->foreign('substitute_id')->references('id')->on('employees');
            $table->foreign('requester_id')->references('id')->on('users');
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
        Schema::dropIfExists('travel_requests');
    }
};
