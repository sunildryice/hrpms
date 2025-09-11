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
        Schema::create('vehicle_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vehicle_request_type_id');
            $table->unsignedBigInteger('office_id')->nullable()->default(null);
            $table->unsignedBigInteger('fiscal_year_id')->nullable()->default(null);
            $table->string('prefix')->nullable()->default(null);
            $table->unsignedInteger('vehicle_request_number')->nullable()->default(null);
            $table->unsignedInteger('modification_number')->nullable()->default(null);
            $table->unsignedBigInteger('modification_vehicle_request_id')->nullable()->default(null);
            $table->text('purpose_of_travel')->nullable()->default(null);
            $table->string('employee_ids', 500)->nullable()->default(null);
            $table->text('remarks')->nullable()->default(null);
            $table->dateTime('start_datetime')->nullable()->default(null);
            $table->dateTime('end_datetime')->nullable()->default(null);
            $table->string('vehicle_type_ids')->nullable()->default(null);
            $table->string('other_remarks')->nullable()->default(null);
            $table->unsignedTinyInteger('for_hours_flag')->nullable()->default(null);
            $table->unsignedTinyInteger('for_hours')->nullable()->default(null);
            $table->string('for_hours_other_remarks')->nullable()->default(null);
            $table->time('pickup_time')->nullable()->default(null);
            $table->string('pickup_place')->nullable()->default(null);
            $table->string('travel_from')->nullable()->default(null);
            $table->string('destination')->nullable()->default(null);
            $table->time('end_time')->nullable()->default(null);
            $table->unsignedMediumInteger('number_overnight_stay')->nullable()->default(null);
            $table->unsignedMediumInteger('extra_travel')->nullable()->default(null);
            $table->decimal('tentative_cost', 10, 2)->nullable()->default(null);
            $table->unsignedBigInteger('activity_code_id')->nullable()->default(null);
            $table->unsignedBigInteger('account_code_id')->nullable()->default(null);
            $table->unsignedBigInteger('donor_code_id')->nullable()->default(null);
            $table->string('district_ids', 500)->nullable()->default(null);

            $table->unsignedBigInteger('assigned_vehicle_id')->nullable()->default(null);
            $table->dateTime('assigned_departure_datetime')->nullable()->default(null);
            $table->dateTime('assigned_arrival_datetime')->nullable()->default(null);
            $table->text('assigned_remarks')->nullable()->default(null);

            $table->unsignedBigInteger('status_id')->nullable()->default(null);
            $table->unsignedBigInteger('requester_id')->nullable()->default(null);
            $table->unsignedBigInteger('reviewer_id')->nullable()->default(null);
            $table->unsignedBigInteger('approver_id')->nullable()->default(null);
            $table->unsignedBigInteger('created_by')->nullable()->default(null);
            $table->unsignedBigInteger('updated_by')->nullable()->default(null);
            $table->nullableTimestamps();

            $table->foreign('activity_code_id')->references('id')->on('lkup_activity_codes');
            $table->foreign('account_code_id')->references('id')->on('lkup_account_codes');
            $table->foreign('donor_code_id')->references('id')->on('lkup_donor_codes');
            $table->foreign('office_id')->references('id')->on('lkup_offices');
            $table->foreign('reviewer_id')->references('id')->on('users');
            $table->foreign('approver_id')->references('id')->on('users');
            $table->foreign('requester_id')->references('id')->on('users');
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
        Schema::dropIfExists('vehicle_requests');
    }
};
