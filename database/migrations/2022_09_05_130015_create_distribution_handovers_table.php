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
        Schema::create('distribution_handovers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('distribution_request_id');
            $table->unsignedBigInteger('fiscal_year_id')->nullable()->default(null);
            $table->unsignedBigInteger('office_id')->nullable()->default(null);
            $table->string('prefix')->nullable()->default(null);
            $table->unsignedInteger('distribution_handover_number')->nullable()->default(null);
            $table->unsignedBigInteger('project_code_id')->nullable()->default(null);
            $table->unsignedBigInteger('district_id')->nullable()->default(null);
            $table->unsignedBigInteger('local_level_id')->nullable()->default(null);
            $table->string('to_name')->nullable()->default(null);
            $table->text('letter_body')->nullable()->default(null);
            $table->string('cc_name')->nullable()->default(null);
            $table->string('health_facility_name')->nullable()->default(null);
            $table->text('remarks')->nullable()->default(null);
            $table->decimal('total_amount', 15,2)->default(0);
            $table->unsignedBigInteger('approver_id')->nullable()->default(null);
            $table->unsignedBigInteger('status_id')->nullable()->default(null);
            $table->unsignedBigInteger('created_by')->nullable()->default(null);
            $table->unsignedBigInteger('updated_by')->nullable()->default(null);
            $table->nullableTimestamps();

            $table->foreign('distribution_request_id')->references('id')->on('distribution_requests');
            $table->foreign('project_code_id')->references('id')->on('lkup_project_codes');
            $table->foreign('local_level_id')->references('id')->on('lkup_local_levels');
            $table->foreign('created_by')->references('id')->on('users');
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
        Schema::dropIfExists('distribution_handovers');
    }
};
