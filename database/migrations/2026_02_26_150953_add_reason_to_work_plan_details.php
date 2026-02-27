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
        Schema::table('work_plan_details', function (Blueprint $table) {
            if (!Schema::hasColumn('work_plan_details', 'reason')) {
                $table->text('reason')->nullable()->after('status');
            }

            if (!Schema::hasColumn('work_plan_details', 'work_plan_date')) {
                $table->date('work_plan_date')->nullable()->after('work_plan_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('work_plan_details', function (Blueprint $table) {
            if (Schema::hasColumn('work_plan_details', 'reason')) {
                $table->dropColumn('reason');
            }
            if (Schema::hasColumn('work_plan_details', 'work_plan_date')) {
                $table->dropColumn('work_plan_date');
            }
        });
    }
};
