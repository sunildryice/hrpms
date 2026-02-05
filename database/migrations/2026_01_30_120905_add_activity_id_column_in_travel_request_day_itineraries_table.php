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
            $table->unsignedBigInteger('activity_id')->nullable()->default(null)->after('travel_request_id');
            $table->foreign('activity_id')->references('id')->on('project_activities');
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
            $table->dropForeign('travel_request_day_itineraries_activity_id_foreign');
            $table->dropColumn('activity_id');
        });
    }
};
