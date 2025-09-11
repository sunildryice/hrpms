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
        Schema::create('travel_claim_itineraries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('travel_claim_id');
            $table->unsignedBigInteger('travel_itinerary_id');
            $table->unsignedMediumInteger('overnights')->nullable()->default(null);
            $table->decimal('percentage_charged', 5, 2)->nullable()->default(null);
            $table->decimal('total_amount', 10, 2)->nullable()->default(null);
            $table->string('attachment')->nullable()->default(null);
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by')->nullable()->default(null);
            $table->nullableTimestamps();

            $table->foreign('travel_claim_id')->references('id')->on('travel_claims');
            $table->foreign('travel_itinerary_id')->references('id')->on('travel_request_itineraries');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('travel_claim_itineraries');
    }
};
