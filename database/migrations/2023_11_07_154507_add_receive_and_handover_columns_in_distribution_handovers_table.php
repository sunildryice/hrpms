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
            $table->unsignedBigInteger('receiver_id')->default(null)->nullable()->after('status_id');
            $table->date('received_date')->default(null)->nullable()->after('receiver_id');
            $table->text('receiver_remarks')->default(null)->nullable()->after('received_date');
            $table->date('handover_date')->default(null)->nullable()->after('receiver_remarks');
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
            $table->dropColumn('receiver_id');
            $table->dropColumn('received_date');
            $table->dropColumn('handover_date');
            $table->dropColumn('receiver_remarks');
        });
    }
};
