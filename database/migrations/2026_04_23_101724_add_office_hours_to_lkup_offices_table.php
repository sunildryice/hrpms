<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('lkup_offices', function (Blueprint $table) {
            $table->time('office_checkin_time')->nullable()->default(null)->after('weekend_type');
            $table->time('office_checkout_time')->nullable()->default(null)->after('office_checkin_time');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lkup_offices', function (Blueprint $table) {
            $table->dropColumn(['office_checkin_time', 'office_checkout_time']);
        });
    }
};
