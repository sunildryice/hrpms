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
        Schema::table('travel_request_day_itineraries', function (Blueprint $table) {
            $table->text('comprehensive_activity_description')->nullable()->after('planned_activities');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('travel_request_day_itineraries', function (Blueprint $table) {
            $table->dropColumn('comprehensive_activity_description');
        });
    }
};
