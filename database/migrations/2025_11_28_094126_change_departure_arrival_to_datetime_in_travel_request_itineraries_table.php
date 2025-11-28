<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('travel_request_itineraries', function (Blueprint $table) {
            $table->dateTime('departure_date')->nullable()->change();
            $table->dateTime('arrival_date')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('travel_request_itineraries', function (Blueprint $table) {
            $table->date('departure_date')->nullable()->change();
            $table->date('arrival_date')->nullable()->change();
        });
    }
};
