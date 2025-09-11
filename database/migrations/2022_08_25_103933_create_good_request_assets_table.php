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
        Schema::create('good_request_assets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('good_request_id')->nullable()->default(null);
            $table->unsignedBigInteger('good_request_item_id')->nullable()->default(null);
            $table->unsignedBigInteger('assign_asset_id');
            $table->unsignedBigInteger('asset_condition_id')->nullable()->default(null);
            $table->unsignedTinyInteger('status')->default(2);

            $table->unsignedBigInteger('assigned_user_id')->nullable()->default(null);
            $table->unsignedBigInteger('assigned_district_id')->nullable()->default(null);
            $table->unsignedBigInteger('assigned_office_id')->nullable()->default(null);
            $table->unsignedBigInteger('assigned_department_id')->nullable()->default(null);
            $table->date('assigned_on')->nullable()->default(null);

            $table->unsignedBigInteger('reviewer_id')->nullable()->default(null);
            $table->unsignedBigInteger('approver_id')->nullable()->default(null);
            $table->unsignedBigInteger('handover_status_id')->nullable()->default(null);
            $table->nullableTimestamps();

            $table->foreign('good_request_id')->references('id')->on('good_requests');
            $table->foreign('good_request_item_id')->references('id')->on('good_request_items')->onDelete('cascade');
            $table->foreign('assign_asset_id')->references('id')->on('assets');
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
        Schema::dropIfExists('good_request_assets');
    }
};
