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
        Schema::create('vehicle_request_procurements', function (Blueprint $table) {
            $table->unsignedBigInteger('vehicle_request_id');
            $table->unsignedBigInteger('officer_id');

            $table->foreign('vehicle_request_id')
                ->references('id')
                ->on('vehicle_requests')
                ->onDelete('cascade');
            $table->foreign('officer_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
            $table->primary(['vehicle_request_id','officer_id'], 'vehicle_procurement_primary');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vehicle_request_procurements');
    }
};
