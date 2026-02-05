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
        Schema::create('payment_sheet_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('payment_sheet_id');
            $table->unsignedBigInteger('payment_bill_id');
            $table->unsignedBigInteger('activity_code_id')->nullable()->default(null);
            $table->unsignedBigInteger('account_code_id')->nullable()->default(null);
            $table->unsignedBigInteger('donor_code_id')->nullable()->default(null);
            $table->unsignedBigInteger('processed_by_office_id')->nullable()->default(null);
            $table->unsignedBigInteger('charged_office_id')->nullable()->default(null);
            $table->unsignedTinyInteger('percentage')->nullable()->default(null);
            $table->decimal('total_amount', 12 ,2)->nullable()->default(null);
            $table->decimal('vat_amount', 12 ,2)->nullable()->default(null);
            $table->decimal('amount_with_vat', 12,2)->nullable()->default(null);
            $table->decimal('tds_amount',12,2)->nullable()->default(null);
            $table->decimal('net_amount',12,2)->nullable()->default(null);
            $table->text('description')->nullable()->default(null);
            $table->nullableTimestamps();

            $table->foreign('payment_sheet_id')->references('id')->on('payment_sheets');
            $table->foreign('payment_bill_id')->references('id')->on('payment_bills');
            $table->foreign('activity_code_id')->references('id')->on('lkup_activity_codes');
            $table->foreign('account_code_id')->references('id')->on('lkup_account_codes');
            $table->foreign('donor_code_id')->references('id')->on('lkup_donor_codes');
            $table->foreign('processed_by_office_id')->references('id')->on('lkup_offices');
            $table->foreign('charged_office_id')->references('id')->on('lkup_offices');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payment_sheet_details');
    }
};
