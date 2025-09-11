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
        Schema::create('purchase_request_procurements', function (Blueprint $table) {
            $table->unsignedBigInteger('purchase_request_id');
            $table->unsignedBigInteger('officer_id');

            $table->foreign('purchase_request_id')
                ->references('id')
                ->on('purchase_requests')
                ->onDelete('cascade');
            $table->foreign('officer_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
            
            $table->primary(['purchase_request_id','officer_id'], 'purchase_procurement_primary');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('purchase_request_procurements');
    }
};
