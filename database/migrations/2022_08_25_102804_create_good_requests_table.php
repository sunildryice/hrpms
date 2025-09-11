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
        Schema::create('good_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('fiscal_year_id')->nullable()->default(null);
            $table->unsignedBigInteger('office_id')->nullable()->default(null);
            $table->unsignedBigInteger('project_code_id')->nullable()->default(null);
            $table->string('prefix')->nullable()->default(null);
            $table->unsignedInteger('good_request_number')->nullable()->default(null);
            $table->boolean('is_direct_dispatch')->nullable()->default(0);
            $table->text('purpose')->nullable()->default(null);
            $table->text('receiver_note')->nullable()->default(null);
            $table->unsignedBigInteger('reviewer_id')->nullable()->default(null);
            $table->unsignedBigInteger('approver_id')->nullable()->default(null);
            $table->unsignedBigInteger('logistic_officer_id')->nullable()->default(null);
            $table->unsignedBigInteger('status_id')->nullable()->default(null);
            $table->unsignedBigInteger('created_by')->nullable()->default(null);
            $table->unsignedBigInteger('updated_by')->nullable()->default(null);
            $table->nullableTimestamps();

            $table->foreign('fiscal_year_id')->references('id')->on('lkup_fiscal_years');
            $table->foreign('project_code_id')->references('id')->on('lkup_project_codes');
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
        Schema::dropIfExists('good_requests');
    }
};
