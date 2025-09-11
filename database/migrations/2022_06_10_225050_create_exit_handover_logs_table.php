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
        Schema::create('exit_handover_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('handover_note_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('original_user_id')->nullable()->default(null);
            $table->text('log_remarks')->nullable()->default(null);
            $table->unsignedBigInteger('status_id');
            $table->nullableTimestamps();

            $table->foreign('handover_note_id')->references('id')->on('exit_handover_notes');
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('original_user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('exit_handover_logs');
    }
};
