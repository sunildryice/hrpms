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
        Schema::create('disposition_request_assets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('disposition_request_id')->nullable()->default(null);
            $table->unsignedBigInteger('asset_id')->nullable()->default(null);
            $table->text('disposition_reason')->nullable()->default(null);
            $table->unsignedBigInteger('created_by')->nullable()->default(null);
            $table->unsignedBigInteger('updated_by')->nullable()->default(null);

            $table->foreign('disposition_request_id')->references('id')->on('disposition_requests');
            $table->foreign('asset_id')->references('id')->on('assets');
            $table->foreign('created_by')->references('id')->on('users');

            $table->nullableTimestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('disposition_request_assets');
    }
};
