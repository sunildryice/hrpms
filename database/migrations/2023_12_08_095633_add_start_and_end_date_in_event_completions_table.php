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
        Schema::table('event_completions', function (Blueprint $table) {
            $table->dropColumn('program_date');
            $table->date('start_date')->nullable()->default(null)->after('venue');
            $table->date('end_date')->nullable()->default(null)->after('start_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('event_completions', function (Blueprint $table) {
            $table->dateTime('program_date')->nullable()->default(null)->after('venue');            
            $table->dropColumn('start_date');
            $table->dropColumn('end_date');
        });
    }
};
