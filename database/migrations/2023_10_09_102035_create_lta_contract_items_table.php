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
        Schema::create('lta_contract_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('lta_contract_id');
            $table->unsignedBigInteger('item_id');
            $table->unsignedBigInteger('unit_id');
            $table->longText('specification')->nullable()->default(null);
            $table->decimal('unit_price', 15, 2)->default(0);

            $table->nullableTimestamps();

            $table->foreign('lta_contract_id')->references('id')->on('lta_contracts');
            $table->foreign('item_id')->references('id')->on('lkup_items');
            $table->foreign('unit_id')->references('id')->on('lkup_measurement_units');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lta_contract_items');
    }
};
