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
        Schema::create('exit_handover_attachments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('handover_note_id');
            $table->string('attachment_type')->nullable()->default(null);
            $table->string('attachment_name')->nullable()->default(null);
            $table->string('attachment')->nullable()->default(null);
            $table->nullableTimestamps();

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
        Schema::dropIfExists('exit_handover_attachments');
    }
};
