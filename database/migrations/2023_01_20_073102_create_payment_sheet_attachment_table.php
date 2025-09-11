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
        Schema::create('payment_sheet_attachments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('payment_sheet_id')->nullable()->default(null);
            $table->longText('title')->nullable()->default(null);
            $table->longText('attachment')->nullable()->default(null);
            $table->longText('link')->nullable()->default(null);
            $table->unsignedBigInteger('created_by')->nullable()->default(null);
            $table->foreign('payment_sheet_id')->references('id')->on('payment_sheets')->onDelete('cascade');
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
        Schema::dropIfExists('payment_sheet_attachments');
    }
};
