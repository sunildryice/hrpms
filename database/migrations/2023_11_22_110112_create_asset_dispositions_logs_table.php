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
        Schema::create('asset_dispositions_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('asset_disposition_id')->nullable()->default(null);
            $table->unsignedBigInteger('user_id')->nullable()->default(null);
            $table->unsignedBigInteger('original_user_id')->nullable()->default(null);
            $table->text('log_remarks')->nullable()->default(null);
            $table->unsignedBigInteger('status_id')->nullable()->default(null);
            $table->nullableTimestamps();

            $table->foreign('asset_disposition_id')->references('id')->on('asset_dispositions');
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
        Schema::dropIfExists('asset_dispositions_logs');
    }
};
