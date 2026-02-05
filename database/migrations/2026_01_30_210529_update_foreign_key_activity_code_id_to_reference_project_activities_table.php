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
        Schema::table('travel_claim_local_travel', function (Blueprint $table) {
            $table->dropForeign('travel_claim_local_travel_activity_code_id_foreign');

            $table->foreign('activity_code_id')->references('id')->on('project_activities');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('travel_claim_local_travel', function (Blueprint $table) {
            $table->dropForeign('travel_claim_local_travel_activity_code_id_foreign');

            $table->foreign('activity_code_id')->references('id')->on('lkup_activity_codes');
        });
    }
};
