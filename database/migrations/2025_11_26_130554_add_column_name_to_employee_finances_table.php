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
        Schema::table('employee_finances', function (Blueprint $table) {
            $table->string('account_holder_name')->nullable()->after('pf_number');

            $table->dropColumn(['ssf_number', 'remote_category']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('employee_finances', function (Blueprint $table) {
            $table->dropColumn('account_holder_name');

            $table->string('ssf_number')->nullable()->after('employee_id');
            $table->string('remote_category')->nullable()->after('branch_name');
        });
    }
};
