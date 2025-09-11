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
        // Schema::table('work_plan_daily_logs', function (Blueprint $table) {
        //     $table->dropForeign('work_plan_daily_logs_work_plan_id_foreign');
        //     $table->dropIndex('work_plan_daily_logs_work_plan_id_log_date_unique');
        //     $table->foreign('work_plan_id')->references('id')->on('work_plans');
        //
        //     $table->after('activity_area_id', function (Blueprint $table) {
        //         $table->unsignedBigInteger('donor_id')->nullable()->default(null);
        //     });
        //
        //     $table->foreign('donor_id')->references('id')->on('lkup_donor_codes')->onDelete('restrict');
        //     $table->unique(['work_plan_id', 'donor_id',  'log_date'], 'work_plan_donor_log_date_unique');
        // });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Schema::table('work_plan_daily_logs', function (Blueprint $table) {
        //     $table->dropForeign('work_plan_daily_logs_work_plan_id_foreign');
        //     $table->dropForeign('work_plan_daily_logs_donor_id_foreign');
        //
        //     $table->dropIndex('work_plan_donor_log_date_unique');
        //
        //     $table->dropColumn(['donor_id']);
        //
        //     $table->foreign('work_plan_id')->references('id')->on('work_plans');
        //     $table->unique(['work_plan_id', 'log_date']);
        // });
    }
};
