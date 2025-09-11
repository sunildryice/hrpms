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
        Schema::dropIfExists('asset_dispositions_logs');
        Schema::dropIfExists('asset_dispositions');

        Schema::create('disposition_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('disposition_type_id')->nullable()->default(null);
            $table->dateTime('disposition_date')->nullable()->default(null);
            $table->unsignedBigInteger('requester_id')->nullable()->default(null);
            $table->unsignedBigInteger('reviewer_id')->nullable()->default(null);
            $table->unsignedBigInteger('approver_id')->nullable()->default(null);
            $table->unsignedBigInteger('office_id')->nullable()->default(null);
            $table->unsignedBigInteger('created_by')->nullable()->default(null);
            $table->unsignedBigInteger('updated_by')->nullable()->default(null);
            $table->unsignedBigInteger('status_id')->nullable()->default(null);

            $table->foreign('disposition_type_id')->references('id')->on('lkup_disposition_types');
            $table->foreign('requester_id')->references('id')->on('users');
            $table->foreign('reviewer_id')->references('id')->on('users');
            $table->foreign('approver_id')->references('id')->on('users');
            $table->foreign('office_id')->references('id')->on('lkup_offices');
            $table->foreign('created_by')->references('id')->on('users');

            $table->nullableTimestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('disposition_requests');

        Schema::create('asset_dispositions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('asset_id')->nullable()->default(null);
            $table->unsignedBigInteger('disposition_type_id')->nullable()->default(null);
            $table->dateTime('disposition_date')->nullable()->default(null);
            $table->text('disposition_reason')->nullable()->default(null);
            $table->unsignedBigInteger('requester_id')->nullable()->default(null);
            $table->unsignedBigInteger('reviewer_id')->nullable()->default(null);
            $table->unsignedBigInteger('approver_id')->nullable()->default(null);
            $table->unsignedBigInteger('office_id')->nullable()->default(null);
            $table->unsignedBigInteger('created_by')->nullable()->default(null);
            $table->unsignedBigInteger('updated_by')->nullable()->default(null);
            $table->unsignedBigInteger('status_id')->nullable()->default(null);

            $table->foreign('asset_id')->references('id')->on('assets');
            $table->foreign('disposition_type_id')->references('id')->on('lkup_disposition_types');
            $table->foreign('requester_id')->references('id')->on('users');
            $table->foreign('reviewer_id')->references('id')->on('users');
            $table->foreign('approver_id')->references('id')->on('users');
            $table->foreign('office_id')->references('id')->on('lkup_offices');
            $table->foreign('created_by')->references('id')->on('users');

            $table->nullableTimestamps();
        });

        Schema::create('asset_dispositions_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('asset_disposition_id')->nullable()->default(null);
            $table->unsignedBigInteger('user_id')->nullable()->default(null);
            $table->unsignedBigInteger('original_user_id')->nullable()->default(null);
            $table->text('log_remarks')->nullable()->default(null);
            $table->unsignedBigInteger('status_id')->nullable()->default(null);
            $table->nullableTimestamps();

            $table->foreign('asset_disposition_id')->references('id')->on('asset_dispositions');
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('original_user_id')->references('id')->on('users');
        });
    }
};
