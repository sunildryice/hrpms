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
        Schema::create('travel_authorization_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('office_id')->nullable();
            $table->unsignedBigInteger('requester_id');
            $table->unsignedBigInteger('reviewer_id')->nullable()->default(null);
            $table->unsignedBigInteger('recommender_id')->nullable()->default(null);
            $table->unsignedBigInteger('approver_id')->nullable()->default(null);
            $table->unsignedBigInteger('fiscal_year_id')->nullable()->default(null);
            $table->string('prefix')->nullable()->default(null);
            $table->unsignedInteger('ta_number')->nullable()->default(null);
            $table->unsignedInteger('modification_number')->nullable()->default(null);
            $table->unsignedBigInteger('modification_ta_request_id')->nullable()->default(null);
            $table->date('request_date');
            $table->text('objectives')->nullable()->default(null);
            $table->text('outcomes')->nullable()->default(null);
            $table->text('remarks')->nullable()->default(null);
            $table->unsignedBigInteger('status_id')->nullable()->default(null);
            $table->unsignedBigInteger('created_by')->nullable()->default(null);
            $table->unsignedBigInteger('updated_by')->nullable()->default(null);
            $table->nullableTimestamps();

            $table->foreign('office_id')->references('id')->on('lkup_offices');
            $table->foreign('fiscal_year_id')->references('id')->on('lkup_fiscal_years');
            $table->foreign('requester_id')->references('id')->on('users');
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
        Schema::dropIfExists('travel_authorization_requests');
    }
};
