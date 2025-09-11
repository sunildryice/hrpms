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
        Schema::create('travel_request_estimates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('travel_request_id');
            $table->decimal('estimated_dsa', 10, 2)->nullable()->default(null);
            $table->decimal('estimated_air_fare', 10, 2)->nullable()->default(null);
            $table->decimal('estimated_vehicle_fare', 10, 2)->nullable()->default(null);
            $table->decimal('advance_amount', 10, 2)->nullable()->default(null);
            $table->decimal('miscellaneous_amount', 10, 2)->nullable()->default(null);
            $table->text('miscellaneous_remarks')->nullable()->default(null);
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by')->nullable()->default(null);
            $table->nullableTimestamps();

            $table->foreign('travel_request_id')->references('id')->on('travel_requests');
            $table->foreign('created_by')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('travel_request_estimates');
    }
};
