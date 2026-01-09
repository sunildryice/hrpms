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
        Schema::create('travel_request_day_itineraries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('travel_request_id');

            $table->date('date')->nullable()->default(null);                          
            $table->text('planned_activities')->nullable()->default(null); 
            $table->boolean('accommodation')->default(false);
            $table->boolean('air_ticket')->default(false);
            $table->string('departure_place')->nullable()->default(null);
            $table->string('arrival_place')->nullable()->default(null);
            $table->string('departure_time')->nullable()->default(null);
            $table->boolean('vehicle')->default(false);
            $table->string('vehicle_request_form_link')->nullable();
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
        Schema::dropIfExists('travel_request_day_itineraries');
    }
};
