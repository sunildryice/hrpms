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
        Schema::table('fund_request_activities', function (Blueprint $table) {
            $table->dropColumn('project_target_unit');
            $table->dropColumn('dip_target_unit');
            $table->dropColumn('required_amount');
        });

        Schema::table('fund_request_activities', function (Blueprint $table) {
            $table->decimal('variance_budget_amount', 15,2)->default(0);
            $table->decimal('project_target_unit', 10, 2)->default(0);
            $table->decimal('dip_target_unit', 10, 2)->default(0);
            $table->decimal('variance_target_unit', 10,2)->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('fund_request_activities', function (Blueprint $table) {
            $table->dropColumn('variance_budget_amount');
            $table->dropColumn('variance_target_unit');
            $table->decimal('required_amount', 10, 2)->default(0);
        });
    }
};
