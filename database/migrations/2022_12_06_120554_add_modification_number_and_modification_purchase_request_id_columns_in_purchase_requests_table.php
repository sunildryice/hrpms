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
        Schema::table('purchase_requests', function (Blueprint $table) {
            $table->unsignedBigInteger('modification_number')->nullable()->default(null)->after('purchase_number');
            $table->unsignedBigInteger('modification_purchase_request_id')->nullable()->default(null)->after('modification_number');
            $table->text('modification_remarks')->nullable()->default(null)->after('modification_purchase_request_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('purchase_requests', function (Blueprint $table) {
            $table->dropColumn('modification_number');
            $table->dropColumn('modification_purchase_request_id');
            $table->dropColumn('modification_remarks');
        });
    }
};
