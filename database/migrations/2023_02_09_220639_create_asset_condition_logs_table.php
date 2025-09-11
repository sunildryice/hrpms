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
        Schema::create('asset_condition_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('asset_id')->nullable()->default(null);
            $table->unsignedBigInteger('condition_id')->nullable()->default(null);
            $table->longText('description')->nullable()->default(null);
            $table->unsignedBigInteger('created_by')->nullable()->default(null);
            $table->unsignedBigInteger('updated_by')->nullable()->default(null);
            $table->timestamps();

            $table->foreign('asset_id')->references('id')->on('assets');
            $table->foreign('condition_id')->references('id')->on('lkup_conditions');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('asset_condition_logs');
    }
};
