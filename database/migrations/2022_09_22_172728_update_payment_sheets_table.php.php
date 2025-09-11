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
            $table->unsignedBigInteger('district_id')->nullable()->default(null)->after('sheet_number');
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
            $table->dropColumn('district_id');
        });
    }
};
