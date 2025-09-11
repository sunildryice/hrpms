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
        Schema::create('advance_settlement_attachments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('advance_settlement_id')->nullable()->default(null);
            $table->longText('title')->nullable()->default(null);
            $table->longText('attachment')->nullable()->default(null);
            $table->unsignedBigInteger('created_by')->nullable()->default(null);
            $table->foreign('advance_settlement_id')->references('id')->on('advance_settlements')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('advance_settlement_attachments');
    }
};
