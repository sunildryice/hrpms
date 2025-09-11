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
        Schema::table('good_requests', function (Blueprint $table) {
            $table->unsignedBigInteger('receiver_id')->nullable()->default(null)->after('receiver_note');
            $table->dateTime('received_at')->nullable()->default(null)->after('receiver_id');
            $table->boolean('is_direct_assign')->default(false)->after('is_direct_dispatch');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('good_requests', function (Blueprint $table) {
            $table->dropColumn(['receiver_id', 'received_at','is_direct_assign']);
        });
    }
};
