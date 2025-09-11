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
        Schema::create('travel_authorization_itineraries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('travel_authorization_id');
            $table->date('travel_date')->nullable();
            $table->string('place_from')->nullable();
            $table->string('place_to')->nullable();
            $table->text('activities')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->nullableTimestamps();

            $table->foreign('travel_authorization_id')->references('id')->on('travel_authorization_requests');
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
        Schema::dropIfExists('travel_authorization_itineraries');
    }
};
