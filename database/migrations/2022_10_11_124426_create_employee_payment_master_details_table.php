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
        Schema::table('employee_payment_masters', function (Blueprint $table) {
            $table->dropForeign('employee_payment_masters_payment_item_id_foreign');
            $table->dropColumn('payment_item_id');
            $table->dropColumn('amount');
        });

        Schema::create('employee_payment_master_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('payment_master_id');
            $table->unsignedBigInteger('payment_item_id');
            $table->decimal('amount', 15, 2)->nullable()->default(null);
            $table->unsignedBigInteger('created_by')->nullable()->default(null);
            $table->unsignedBigInteger('updated_by')->nullable()->default(null);
            $table->nullableTimestamps();

            $table->foreign('payment_master_id')->references('id')->on('employee_payment_masters');
            $table->foreign('payment_item_id')->references('id')->on('lkup_payment_items');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('employee_payment_master_details');
        Schema::table('employee_payment_masters', function (Blueprint $table) {
            $table->unsignedBigInteger('payment_item_id')->nullable();
            $table->decimal('amount', 15, 2)->nullable()->default(null);

            $table->foreign('payment_item_id')->references('id')->on('lkup_payment_items');
        });
    }
};
