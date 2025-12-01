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
        Schema::table('lkup_leave_types', function (Blueprint $table) {
            $table->decimal('number_of_days', 3, 1)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('lkup_leave_types', function (Blueprint $table) {
            $table->integer('number_of_days')->change();
        });
    }
};
