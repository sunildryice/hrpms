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
        Schema::table('work_from_homes', function (Blueprint $table) {
            $table->unsignedInteger('work_from_home_number')->nullable();
            $table->unsignedBigInteger('fiscal_year_id')->nullable()->change();
        });

        Schema::table('lieu_leave_requests', function (Blueprint $table) {
            $table->unsignedInteger('lieu_leave_request_number')->nullable();
            $table->unsignedBigInteger('fiscal_year_id')->nullable()->change();
        });

        Schema::table('off_day_works', function (Blueprint $table) {
            $table->unsignedInteger('off_day_work_number')->nullable();
            $table->unsignedBigInteger('fiscal_year_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('work_from_homes', function (Blueprint $table) {
            $table->dropColumn('work_from_home_number');
        });
        Schema::table('lieu_leave_requests', function (Blueprint $table) {
            $table->dropColumn('lieu_leave_request_number');
        });
        Schema::table('off_day_works', function (Blueprint $table) {
            $table->dropColumn('off_day_work_number');
        });
    }
};
