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
        Schema::create('inventory_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('account_code_id')->nullable()->default(null);
            $table->unsignedBigInteger('activity_code_id')->nullable()->default(null);
            $table->unsignedBigInteger('donor_code_id')->nullable()->default(null);
            $table->unsignedBigInteger('grn_id')->nullable()->default(null);
            $table->unsignedBigInteger('grn_item_id')->nullable()->default(null);
            $table->unsignedBigInteger('category_id')->nullable()->default(null);
            $table->unsignedBigInteger('item_id')->nullable()->default(null);
            $table->unsignedBigInteger('unit_id')->nullable()->default(null);
            $table->unsignedBigInteger('supplier_id')->nullable()->default(null);
            $table->unsignedBigInteger('acquisition_method_id')->nullable()->default(null);
            $table->unsignedBigInteger('distribution_type_id')->nullable()->default(1);
            $table->date('expiry_date')->nullable()->default(null);
            $table->string('item_name')->nullable()->default(null);
            $table->string('model_name')->nullable()->default(null);
            $table->text('specification')->nullable()->default(null);
            $table->date('purchase_date')->nullable()->default(null);
            $table->unsignedInteger('quantity')->nullable()->default(null);
            $table->decimal('unit_price', 15, 2)->nullable()->default(null);
            $table->decimal('total_price', 15, 2)->nullable()->default(null);
            $table->decimal('vat_amount', 15, 2)->nullable()->default(null);
            $table->decimal('total_amount', 15, 2)->nullable()->default(null);
            $table->unsignedInteger('assigned_quantity')->default(0);
            $table->string('voucher_number')->default(0);
            $table->unsignedBigInteger('execution_id')->default(0);
            $table->unsignedBigInteger('created_by')->nullable()->default(null);
            $table->unsignedBigInteger('updated_by')->nullable()->default(null);
            $table->nullableTimestamps();

            $table->foreign('grn_id')->references('id')->on('grns');
            $table->foreign('grn_item_id')->references('id')->on('grn_items');
            $table->foreign('supplier_id')->references('id')->on('suppliers');
            $table->foreign('item_id')->references('id')->on('lkup_items');
            $table->foreign('unit_id')->references('id')->on('lkup_measurement_units');
            $table->foreign('category_id')->references('id')->on('lkup_inventory_categories');
            $table->foreign('activity_code_id')->references('id')->on('lkup_activity_codes');
            $table->foreign('account_code_id')->references('id')->on('lkup_account_codes');
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
        Schema::dropIfExists('inventory_items');
    }
};
