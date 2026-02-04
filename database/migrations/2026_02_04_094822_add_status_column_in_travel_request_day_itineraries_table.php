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
            $table->enum('status', ['not_started', 'under_progress', 'no_required', 'completed'])->nullable()->after('remarks');
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
            $table->dropColumn(['status']);
        });
    }
};
