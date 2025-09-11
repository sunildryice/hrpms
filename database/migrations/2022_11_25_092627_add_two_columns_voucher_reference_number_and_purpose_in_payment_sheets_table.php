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
        Schema::table('payment_sheets', function (Blueprint $table) {
            $table->string('voucher_reference_number')->nullable()->default(null)->after('net_amount');
            $table->longText('purpose')->nullable()->default(null)->after('voucher_reference_number');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('payment_sheets', function (Blueprint $table) {
            $table->dropColumn('voucher_reference_number');
            $table->dropColumn('purpose');
        });
    }
};
