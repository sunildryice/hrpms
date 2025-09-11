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
        Schema::table('exit_handover_notes', function (Blueprint $table) {
            $table->unsignedBigInteger('approver_id')->nullable()->default(null)->after('employee_id');
            $table->foreign('approver_id')->references('id')->on('users');
        });
        Schema::table('exit_interviews', function (Blueprint $table) {
            $table->unsignedBigInteger('approver_id')->nullable()->default(null)->after('employee_id');
            $table->foreign('approver_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('exit_handover_notes', function (Blueprint $table) {
            $table->dropConstrainedForeignId('approver_id');
        });
        Schema::table('exit_interviews', function (Blueprint $table) {
            $table->dropConstrainedForeignId('approver_id');
        });
    }
};
