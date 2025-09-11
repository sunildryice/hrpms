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
        Schema::create('purchase_request_districts', function (Blueprint $table) {
            $table->unsignedBigInteger('purchase_request_id');
            $table->unsignedBigInteger('district_id');

            $table->foreign('purchase_request_id')->references('id')->on('purchase_requests')->onDelete('cascade');
            $table->foreign('district_id')->references('id')->on('lkup_districts');

            $table->primary(['purchase_request_id','district_id'], 'pk_pr_district');
        });

        Schema::create('purchase_order_districts', function (Blueprint $table) {
            $table->unsignedBigInteger('purchase_order_id');
            $table->unsignedBigInteger('district_id');

            $table->foreign('purchase_order_id')->references('id')->on('purchase_orders')->onDelete('cascade');
            $table->foreign('district_id')->references('id')->on('lkup_districts');

            $table->primary(['purchase_order_id','district_id'], 'pk_po_district');
        });

        Schema::table('purchase_requests', function (Blueprint $table) {
            $table->dropColumn('district_id');
        });
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->dropColumn('district_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('purchase_request_districts');
        Schema::dropIfExists('purchase_order_districts');
        Schema::table('purchase_requests', function (Blueprint $table) {
            $table->unsignedBigInteger('district_id')->nullable()->default(null)->after('fiscal_year_id');
        });
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->unsignedBigInteger('district_id')->nullable()->default(null)->after('fiscal_year_id');
        });
    }
};
