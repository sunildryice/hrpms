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
        Schema::create('transportation_bill_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('transportation_bill_id');
            $table->unsignedMediumInteger('quantity')->default(0);
            $table->text('item_description')->nullable()->default(null);
            $table->text('remarks')->nullable()->default(null);
            $table->nullableTimestamps();

            $table->foreign('transportation_bill_id')->references('id')->on('transportation_bills');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transportation_bill_details');
    }
};
