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
        Schema::create('purchase_request_order', function (Blueprint $table) {
            $table->unsignedBigInteger('pr_id');
            $table->unsignedBigInteger('po_id');

            $table->foreign('pr_id')
                ->references('id')
                ->on('purchase_requests')
                ->onDelete('cascade');

            $table->foreign('po_id')
                ->references('id')
                ->on('purchase_orders')
                ->onDelete('cascade');

            $table->primary(['pr_id', 'po_id']);
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('purchase_request_order');
    }
};
