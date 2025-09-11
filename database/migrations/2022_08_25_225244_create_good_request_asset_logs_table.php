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
        Schema::create('good_request_asset_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('good_request_asset_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('original_user_id')->nullable()->default(null);
            $table->text('log_remarks')->nullable()->default(null);
            $table->unsignedBigInteger('handover_status_id');
            $table->nullableTimestamps();

            $table->foreign('good_request_asset_id', 'fk_gr_asset_id')->references('id')->on('good_request_assets');
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('original_user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('good_request_asset_logs');
    }
};
