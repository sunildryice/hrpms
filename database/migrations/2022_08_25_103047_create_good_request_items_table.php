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
        Schema::create('good_request_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('good_request_id');
            $table->unsignedBigInteger('activity_code_id')->nullable()->default(null);
            $table->unsignedBigInteger('account_code_id')->nullable()->default(null);
            $table->unsignedBigInteger('donor_code_id')->nullable()->default(null);
            $table->text('item_name')->nullable()->default(null);
            $table->unsignedBigInteger('unit_id')->nullable()->default(null);
            $table->unsignedMediumInteger('quantity')->default(0);
            $table->text('specification')->nullable()->default(null);
            $table->unsignedBigInteger('inventory_category_id')->nullable()->default(null);
            $table->unsignedBigInteger('assigned_inventory_item_id')->nullable()->default(null);
            $table->unsignedBigInteger('assigned_item_id')->nullable()->default(null);
            $table->unsignedBigInteger('assigned_unit_id')->nullable()->default(null);
            $table->unsignedMediumInteger('assigned_quantity')->default(0);
            $table->text('assigned_asset_ids')->nullable()->default(null);
            $table->text('assigned_specification')->nullable()->default(null);
            $table->nullableTimestamps();

            $table->foreign('good_request_id')->references('id')->on('good_requests');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('good_request_items');
    }
};
