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
        Schema::table('travel_requests', function (Blueprint $table) {
            $table->unsignedTinyInteger('external_traveler_count')
                ->default(0)
                ->after('purpose_of_travel');

            $table->json('external_travelers')
                ->nullable()
                ->after('external_traveler_count');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('travel_requests', function (Blueprint $table) {
            $table->dropColumn(['external_traveler_count', 'external_travelers']);
        });
    }
};
