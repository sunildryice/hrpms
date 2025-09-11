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
        Schema::create('asset_assign_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('good_request_asset_id')->nullable()->default(null);
            $table->unsignedBigInteger('asset_id')->nullable()->default(null);
            $table->unsignedBigInteger('assigned_office_id')->nullable()->default(null);
            $table->unsignedBigInteger('assigned_department_id')->nullable()->default(null);
            $table->unsignedBigInteger('assigned_user_id')->nullable()->default(null);
            $table->bigInteger('condition_id')->nullable()->default(null);
            $table->longText('remarks')->nullable()->default(null);
            $table->unsignedBigInteger('created_by')->nullable()->default(null);
            $table->unsignedBigInteger('updated_by')->nullable()->default(null);
            $table->nullableTimestamps();

            $table->foreign('asset_id')->references('id')->on('assets')->onDelete('cascade');
            $table->foreign('good_request_asset_id')->references('id')->on('good_request_assets')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('asset_assign_logs');
    }
};
