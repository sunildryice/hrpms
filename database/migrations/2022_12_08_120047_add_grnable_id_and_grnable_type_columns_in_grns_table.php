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
        Schema::table('grns', function (Blueprint $table) {
            $table->unsignedBigInteger('grnable_id')->nullable()->default(null)->after('fiscal_year_id');
            $table->string('grnable_type')->nullable()->default(null)->after('grnable_id');
            $table->dropForeign('grns_purchase_order_id_foreign');
            $table->dropColumn('purchase_order_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('grns', function (Blueprint $table) {
            $table->dropColumn('grnable_id');
            $table->dropColumn('grnable_type');
            $table->unsignedBigInteger('purchase_order_id')->nullable()->default(null)->after('fiscal_year_id');
            $table->foreign('purchase_order_id')->references('id')->on('purchase_orders');
        });
    }
};
