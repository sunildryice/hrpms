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
        Schema::table('attendance_details', function (Blueprint $table) {
            $table->unsignedBigInteger('office_id')->nullable()->after('attendance_master_id');
            $table->unsignedTinyInteger('weekend_type_id')->nullable()->after('office_id');

            $table->foreign('office_id')->references('id')->on('lkup_offices');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('attendance_details', function (Blueprint $table) {
            $table->dropForeign(['office_id']);
            $table->dropColumn(['office_id', 'weekend_type_id']);
        });
    }
};
