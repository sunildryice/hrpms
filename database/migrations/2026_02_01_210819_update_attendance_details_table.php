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
            $table->string('checkin_from')->nullable();
            $table->string('checkout_from')->nullable();

            $table->dropColumn(['charged_hours', 'unrestricted_hours', 'attendance_status']);
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
            $table->dropColumn(['checkin_from', 'checkout_from']);

            $table->double('charged_hours', 4, 2)->nullable()->default(null);
            $table->double('unrestricted_hours', 4, 2)->nullable()->default(null);
            $table->string('attendance_status')->nullable()->default(null);
        });
    }
};
