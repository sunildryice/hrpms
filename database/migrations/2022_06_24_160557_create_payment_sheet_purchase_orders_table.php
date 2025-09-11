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
        Schema::create('payment_sheet_purchase_orders', function (Blueprint $table) {
            $table->unsignedBigInteger('payment_sheet_id');
            $table->unsignedBigInteger('purchase_order_id');

            $table->foreign('payment_sheet_id')
                ->references('id')
                ->on('payment_sheets')
                ->onDelete('cascade');

            $table->foreign('purchase_order_id')
                ->references('id')
                ->on('purchase_orders')
                ->onDelete('cascade');

            $table->primary(['payment_sheet_id','purchase_order_id'], 'pay_sheet_po_pk');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payment_sheet_purchase_orders');
    }
};
