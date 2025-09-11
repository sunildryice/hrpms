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
        Schema::table('inventory_items', function (Blueprint $table) {
            $table->unsignedInteger('batch_number')->nullable()->default(null)->after('model_name');
            $table->unsignedBigInteger('fiscal_year_id')->nullable()->default(null)->after('batch_number');

            $table->foreign('fiscal_year_id')->references('id')->on('lkup_fiscal_years');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('inventory_items', function (Blueprint $table) {
            $table->dropColumn('batch_number');
            $table->dropConstrainedForeignId('fiscal_year_id');
        });
    }
};
