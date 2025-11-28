<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('travel_request_estimates', function (Blueprint $table) {

            $table->decimal('estimated_hotel_accommodation', 10, 2)->nullable()->after('estimated_vehicle_fare');
            $table->decimal('estimated_airport_taxi', 10, 2)->nullable()->after('estimated_hotel_accommodation');
            $table->decimal('estimated_event_activities_cost', 10, 2)->nullable()->after('estimated_airport_taxi');

            $table->renameColumn('advance_amount', 'total_amount');
        });
    }

    public function down()
    {
        Schema::table('travel_request_estimates', function (Blueprint $table) {
            
            $table->renameColumn('total_amount', 'advance_amount');

            $table->dropColumn([
                'estimated_hotel_accommodation',
                'estimated_airport_taxi',
                'estimated_event_activities_cost'
            ]);
        });
    }
};