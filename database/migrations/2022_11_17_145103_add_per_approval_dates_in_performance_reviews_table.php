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
        Schema::table('performance_reviews', function (Blueprint $table) {
            $table->dateTime('goal_setting_date')->nullable()->default(null)->after('approver_id');
            $table->dateTime('mid_term_per_date')->nullable()->default(null)->after('goal_setting_date');
            $table->dateTime('final_per_date')->nullable()->default(null)->after('mid_term_per_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('performance_reviews', function (Blueprint $table) {
            $table->dropColumn('goal_setting_date');
            $table->dropColumn('mid_term_per_date');
            $table->dropColumn('final_per_date');
        });
    }
};
