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
        Schema::table('employee_exit_payables', function (Blueprint $table) {
            $table->date('festival_bonus_date_from')->nullable()->default(null)->after('festival_bonus');
            $table->date('festival_bonus_date_to')->nullable()->default(null)->after('festival_bonus_date_from');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('employee_exit_payables', function (Blueprint $table) {
            $table->dropColumn('festival_bonus_date_from');
            $table->dropColumn('festival_bonus_date_to');
        });
    }
};
