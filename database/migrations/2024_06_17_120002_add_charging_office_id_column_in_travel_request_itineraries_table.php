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
        Schema::table('travel_request_itineraries', function (Blueprint $table) {
            $table->unsignedBigInteger('charging_office_id')->nullable()->after('dsa_category_id');

            $table->foreign('charging_office_id')->references('id')->on('lkup_offices');
        });

        Schema::table('travel_claim_itineraries', function (Blueprint $table) {
            $table->text('description')->nullable()->default(null)->after('attachment');
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
            $table->dropForeign('travel_request_itineraries_charging_office_id_foreign');
            $table->dropColumn('charging_office_id');
        });

        Schema::table('travel_claim_itineraries', function (Blueprint $table) {
                $table->dropColumn('description');
        });
    }
};
