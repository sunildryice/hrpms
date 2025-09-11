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
        Schema::create('exit_handover_activities', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('handover_note_id');
            $table->unsignedBigInteger('activity_code_id');
            $table->string('organization')->nullable()->default(null);
            $table->string('phone')->nullable()->default(null);
            $table->string('email')->nullable()->default(null);
            $table->text('comments')->nullable()->default(null);
            $table->nullableTimestamps();

            $table->foreign('handover_note_id')->references('id')->on('exit_handover_notes');
            $table->foreign('activity_code_id')->references('id')->on('lkup_activity_codes');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('exit_handover_activities');
    }
};
