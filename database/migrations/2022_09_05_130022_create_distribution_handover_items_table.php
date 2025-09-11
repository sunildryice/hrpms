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
        Schema::create('distribution_handover_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('distribution_handover_id');
            $table->unsignedBigInteger('distribution_request_item_id');
            $table->unsignedBigInteger('account_code_id')->nullable()->default(null);
            $table->unsignedBigInteger('activity_code_id')->nullable()->default(null);
            $table->unsignedBigInteger('donor_code_id')->nullable()->default(null);
            $table->unsignedBigInteger('inventory_item_id')->nullable()->default(null);
            $table->unsignedBigInteger('item_id')->nullable()->default(null);
            $table->unsignedBigInteger('unit_id')->nullable()->default(null);
            $table->text('specification')->nullable()->default(null);
            $table->unsignedMediumInteger('quantity')->default(0);
            $table->decimal('unit_price', 15,2)->default(0);
            $table->decimal('total_amount', 15,2)->default(0);
            $table->decimal('vat_amount', 15,2)->default(0);
            $table->decimal('net_amount', 15,2)->default(0);
            $table->nullableTimestamps();

            $table->foreign('distribution_handover_id')->references('id')->on('distribution_handovers');
            $table->foreign('distribution_request_item_id')->references('id')->on('distribution_request_items');
            $table->foreign('account_code_id')->references('id')->on('lkup_account_codes');
            $table->foreign('activity_code_id')->references('id')->on('lkup_activity_codes');
            $table->foreign('donor_code_id')->references('id')->on('lkup_donor_codes');
            $table->foreign('inventory_item_id')->references('id')->on('inventory_items');
            $table->foreign('item_id')->references('id')->on('lkup_items');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('distribution_handover_items');
    }
};
