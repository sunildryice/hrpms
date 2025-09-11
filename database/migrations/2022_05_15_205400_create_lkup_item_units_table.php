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
        Schema::create('lkup_item_units', function (Blueprint $table) {
            $table->unsignedBigInteger('item_id');
            $table->unsignedBigInteger('unit_id');

            $table->foreign('item_id')
                ->references('id')
                ->on('lkup_items')
                ->onDelete('cascade');

            $table->foreign('unit_id')
                ->references('id')
                ->on('lkup_measurement_units')
                ->onDelete('cascade');

            $table->primary(['item_id','unit_id'], 'item_unit_primary');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lkup_item_units');
    }
};
