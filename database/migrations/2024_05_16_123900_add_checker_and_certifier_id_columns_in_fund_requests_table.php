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
        Schema::table('fund_requests', function (Blueprint $table) {
            $table->unsignedBigInteger('checker_id')->nullable()->after('net_amount');
            $table->unsignedBigInteger('certifier_id')->nullable()->after('checker_id');

            $table->foreign('checker_id')->references('id')->on('users');
            $table->foreign('certifier_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('fund_requests', function (Blueprint $table) {
            $table->dropForeign('fund_requests_checker_id_foreign');
            $table->dropForeign('fund_requests_certifier_id_foreign');
            $table->dropColumn(['checker_id', 'certifier_id']);
        });
    }
};
