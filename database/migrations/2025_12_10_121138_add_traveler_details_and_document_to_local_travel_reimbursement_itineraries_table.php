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
        Schema::table('local_travel_reimbursement_itineraries', function (Blueprint $table) {

            $table->unsignedTinyInteger('number_of_travelers')->default(0)->after('travel_mode');
            $table->json('names_of_travelers')->nullable()->after('number_of_travelers');
            $table->string('pickup_location')->nullable()->after('names_of_travelers');
            $table->string('attachment')->nullable()->default(null);

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('local_travel_reimbursement_itineraries', function (Blueprint $table) {

            $table->dropColumn('attachment');
            $table->dropColumn('pickup_location');
            $table->dropColumn('names_of_travelers');
            $table->dropColumn('number_of_travelers');

        });
    }
};
