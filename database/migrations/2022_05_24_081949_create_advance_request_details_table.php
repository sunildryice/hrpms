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
        Schema::create('advance_request_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('advance_request_id');
            $table->unsignedBigInteger('account_code_id')->nullable()->default(null);
            $table->unsignedBigInteger('activity_code_id')->nullable()->default(null);
            $table->unsignedBigInteger('donor_code_id')->nullable()->default(null);
            $table->text('description')->nullable()->default(null);
            $table->decimal('amount', 15,2)->default(0);
            $table->string('attachment')->nullable()->default(null);
            $table->nullableTimestamps();

            $table->foreign('advance_request_id')->references('id')->on('advance_requests');
            $table->foreign('account_code_id')->references('id')->on('lkup_account_codes');
            $table->foreign('activity_code_id')->references('id')->on('lkup_activity_codes');
            $table->foreign('donor_code_id')->references('id')->on('lkup_donor_codes');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('advance_request_details');
    }
};
