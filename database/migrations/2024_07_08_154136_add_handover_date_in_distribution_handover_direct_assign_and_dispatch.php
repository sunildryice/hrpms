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
        Schema::table('distribution_handovers', function (Blueprint $table) {
            $table->date('date_of_handover')->nullable()->after('remarks');
        });
        Schema::table('good_requests', function (Blueprint $table) {
            $table->date('handover_date')->nullable()->after('fiscal_year_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('distribution_handovers', function (Blueprint $table) {
            $table->dropColumn('date_of_handover');
        });
        Schema::table('good_requests', function (Blueprint $table) {
            $table->dropColumn('handover_date');
        });
    }
};
