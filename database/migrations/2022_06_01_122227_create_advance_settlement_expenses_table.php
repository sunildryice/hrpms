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
        Schema::create('advance_settlement_expenses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('advance_settlement_id');
            $table->unsignedBigInteger('advance_request_detail_id');
            $table->unsignedBigInteger('activity_code_id')->nullable()->default(null);
            $table->unsignedBigInteger('account_code_id')->nullable()->default(null);
            $table->unsignedBigInteger('donor_code_id')->nullable()->default(null);
            $table->unsignedBigInteger('district_id')->nullable()->default(null);
            $table->text('narration')->nullable()->default(null);
            $table->text('location')->nullable()->default(null);
            $table->decimal('gross_amount', 12, 2)->nullable()->nullable();
            $table->decimal('tax_amount', 12, 2)->nullable()->nullable();
            $table->decimal('net_amount', 12, 2)->nullable()->nullable();
            $table->unsignedBigInteger('created_by')->nullable()->default(null);
            $table->unsignedBigInteger('updated_by')->nullable()->default(null);
            $table->nullableTimestamps();

            $table->foreign('advance_settlement_id')->references('id')->on('advance_settlements');
            $table->foreign('advance_request_detail_id')->references('id')->on('advance_request_details');
            $table->foreign('activity_code_id')->references('id')->on('lkup_activity_codes');
            $table->foreign('account_code_id')->references('id')->on('lkup_account_codes');
            $table->foreign('donor_code_id')->references('id')->on('lkup_donor_codes');
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
        Schema::dropIfExists('advance_settlement_expenses');
    }
};
