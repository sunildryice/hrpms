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
            $table->unsignedBigInteger('activity_code_id')->nullable()->after('travel_claim_id');
            $table->foreign('activity_code_id')->references('id')->on('lkup_activity_codes');
        });

        Schema::table('travel_dsa_claim', function (Blueprint $table) {
            $table->unsignedBigInteger('activity_code_id')->nullable()->after('travel_claim_id');
            $table->foreign('activity_code_id')->references('id')->on('lkup_activity_codes');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('travel_dsa_claim', function (Blueprint $table) {
            $table->dropForeign(['activity_code_id']);
            $table->dropColumn('activity_code_id');
        });

        Schema::table('travel_claim_local_travel', function (Blueprint $table) {
            $table->dropForeign(['activity_code_id']);
            $table->dropColumn('activity_code_id');
        });
    }
};
