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
            $table->date('last_duty_date')->nullable()->default(null)->after('employee_id');
            $table->date('resignation_date')->nullable()->default(null)->after('last_duty_date');
            $table->boolean('is_insurance')->nullable()->default(null)->after('resignation_date');
        });

        Schema::table('exit_interviews', function (Blueprint $table) {
            $table->unsignedBigInteger('handover_note_id')->after('employee_id');
            $table->foreign('handover_note_id')->references('id')->on('exit_handover_notes');
        });
        Schema::table('employee_exit_payables', function (Blueprint $table) {
            $table->unsignedBigInteger('handover_note_id')->after('employee_id');
            $table->foreign('handover_note_id')->references('id')->on('exit_handover_notes');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('employee_exit_payables', function (Blueprint $table) {
            $table->dropConstrainedForeignId('handover_note_id');
        });
        Schema::table('exit_interviews', function (Blueprint $table) {
            $table->dropConstrainedForeignId('handover_note_id');
        });
        Schema::table('exit_handover_notes', function (Blueprint $table) {
            $table->dropColumn('last_duty_date');
            $table->dropColumn('resignation_date');
        });
    }
};
