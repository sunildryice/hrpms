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
        });
    }
};
