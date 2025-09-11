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
        Schema::table('advance_requests', function (Blueprint $table) {
            $table->date('pay_date')->default(null)->nullable()->after('status_id');
            $table->dateTime('paid_at')->default(null)->nullable()->after('pay_date');
            $table->longText('payment_remarks')->default(null)->nullable()->after('paid_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('advance_requests', function (Blueprint $table) {
            $table->dropColumn(['pay_date', 'paid_at', 'payment_remarks']);
        });
    }
};
