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
        Schema::create('travel_request_substitutes', function (Blueprint $table) {
            $table->unsignedBigInteger('travel_request_id');
            $table->unsignedBigInteger('substitute_id');

            $table->foreign('travel_request_id')->references('id')->on('travel_requests')->onDelete('cascade');
            $table->foreign('substitute_id')->references('id')->on('employees');

            $table->primary(['travel_request_id','substitute_id'], 'pk_tr_substitute');
        });
        Schema::create('travel_itinerary_modes', function (Blueprint $table) {
            $table->unsignedBigInteger('travel_request_itinerary_id');
            $table->unsignedBigInteger('travel_mode_id');

            $table->foreign('travel_request_itinerary_id')->references('id')->on('travel_request_itineraries')->onDelete('cascade');
            $table->foreign('travel_mode_id')->references('id')->on('lkup_travel_modes');

            $table->primary(['travel_request_itinerary_id','travel_mode_id'], 'pl_tr_itinerary_mode');
        });

        Schema::table('travel_request_itineraries', function (Blueprint $table) {
            $table->dropColumn('travel_mode_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('travel_request_substitutes');
        Schema::dropIfExists('travel_itinerary_modes');
        Schema::table('travel_request_itineraries', function (Blueprint $table) {
            $table->unsignedBigInteger('travel_mode_id')->after('travel_request_id')->nullable()->default(null);
        });
    }
};
