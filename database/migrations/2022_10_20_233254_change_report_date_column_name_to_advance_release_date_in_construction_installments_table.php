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
        Schema::table('construction_installments', function (Blueprint $table) {
            $table->renameColumn('report_date', 'advance_release_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('construction_installments', function (Blueprint $table) {
            $table->renameColumn('advance_release_date', 'report_date');
        });
    }
};
