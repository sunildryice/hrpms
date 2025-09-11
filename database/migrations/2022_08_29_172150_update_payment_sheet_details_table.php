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
        Schema::table('payment_sheet_details', function (Blueprint $table) {
            $table->renameColumn('processed_office_id', 'processed_by_office_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('payment_sheet_details', function (Blueprint $table) {
            $table->renameColumn('processed_by_office_id', 'processed_office_id');
        });
    }
};
