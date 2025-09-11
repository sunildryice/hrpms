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
        Schema::table('advance_settlements', function (Blueprint $table) {
            $table->longText('reason_for_over_or_under_spending')->nullable()->default(null)->after('status_id');
            $table->longText('remarks')->nullable()->default(null)->after('reason_for_over_or_under_spending');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('advance_settlements', function (Blueprint $table) {
            $table->dropColumn('reason_for_over_or_under_spending');
            $table->dropColumn('remarks');
        });
    }
};
