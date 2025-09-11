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
        Schema::table('grn_items', function (Blueprint $table) {
            $table->unsignedBigInteger('grnitemable_id')->nullable()->default(null)->after('grn_id');
            $table->string('grnitemable_type')->nullable()->default(null)->after('grnitemable_id');
            $table->dropForeign('grn_items_purchase_order_item_id_foreign');
            $table->dropColumn('purchase_order_item_id');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('grn_items', function (Blueprint $table) {
            $table->dropColumn('grnitemable_id');
            $table->dropColumn('grnitemable_type');
            $table->unsignedBigInteger('purchase_order_item_id')->nullable()->default(null)->after('grn_id');
            $table->foreign('purchase_order_item_id')->references('id')->on('purchase_order_items');
        });
    }
};
