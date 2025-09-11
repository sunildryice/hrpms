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
        Schema::table('employee_address', function (Blueprint $table) {
            $table->longText('current_location')->nullable()->after('temporary_tole')->comment('Google map location url');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('employee_address', function (Blueprint $table) {
            $table->dropColumn('current_location');
        });
    }
};
