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
        Schema::table('lkup_health_facilities', function (Blueprint $table) {
            $table->unsignedBigInteger('province_id')->nullable()->after('title');
            $table->unsignedBigInteger('district_id')->nullable()->after('province_id');
            $table->unsignedBigInteger('local_level_id')->nullable()->after('district_id');
            $table->unsignedBigInteger('ward')->nullable()->after('local_level_id');

            $table->foreign('province_id')->references('id')->on('lkup_provinces');
            $table->foreign('district_id')->references('id')->on('lkup_districts');
            $table->foreign('local_level_id')->references('id')->on('lkup_local_levels');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('lkup_health_facilities', function (Blueprint $table) {
            $table->dropForeign(['province_id']);
            $table->dropForeign(['district_id']);
            $table->dropForeign(['local_level_id']);
            $table->dropColumn(['province_id', 'district_id', 'local_level_id', 'ward']);
        });
    }
};
