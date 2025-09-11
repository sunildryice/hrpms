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
        Schema::table('payment_sheets', function (Blueprint $table) {
            $table->boolean('is_from_po')->default(false)->comment('If the payment sheet is created from a purchase order.')->after('purpose');
        });

        Schema::create('po_item_ps_detail', function(Blueprint $table) {
            $table->unsignedBigInteger('po_item_id');
            $table->unsignedBigInteger('ps_detail_id');

            $table->foreign('po_item_id')
                ->references('id')
                ->on('purchase_order_items')
                ->onDelete('cascade');

            $table->foreign('ps_detail_id')
                ->references('id')
                ->on('payment_sheet_details')
                ->onDelete('cascade');

            $table->primary(['po_item_id', 'ps_detail_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('payment_sheets', function (Blueprint $table) {
            $table->dropColumn('is_from_po');
        });

        Schema::dropIfExists('po_item_ps_detail');
    }
};
