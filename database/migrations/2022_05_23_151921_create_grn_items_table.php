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
        Schema::create('grn_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('grn_id');
            $table->unsignedBigInteger('purchase_order_item_id')->nullable()->default(null);
            $table->unsignedBigInteger('item_id');
            $table->unsignedBigInteger('unit_id');
            $table->unsignedBigInteger('account_code_id')->nullable()->default(null);
            $table->unsignedBigInteger('activity_code_id')->nullable()->default(null);
            $table->unsignedBigInteger('donor_code_id')->nullable()->default(null);
            $table->unsignedMediumInteger('quantity')->default(0);
            $table->decimal('unit_price', 15,2)->default(0);
            $table->decimal('total_price', 15,2)->default(0);
            $table->decimal('discount_amount', 15,2)->default(0);
            $table->decimal('vat_amount', 15,2)->default(0);
            $table->decimal('tds_amount', 15,2)->default(0);
            $table->decimal('total_amount', 15,2)->default(0);
            $table->longText('specification')->nullable()->default(null);
            $table->nullableTimestamps();

            $table->foreign('grn_id')->references('id')->on('grns');
            $table->foreign('purchase_order_item_id')->references('id')->on('purchase_order_items');
            $table->foreign('item_id')->references('id')->on('lkup_items');
            $table->foreign('unit_id')->references('id')->on('lkup_measurement_units');
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
        Schema::dropIfExists('grn_items');
    }
};
