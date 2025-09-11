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
        Schema::create('exit_handover_projects', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('handover_note_id');
            $table->unsignedBigInteger('project_code_id');
            $table->string('project_status')->nullable()->default(null);
            $table->text('action_needed')->nullable()->default(null);
            $table->string('partners')->nullable()->default(null);
            $table->decimal('budget', 12, 2)->nullable()->default(null);
            $table->text('critical_issues')->nullable()->default(null);
            $table->nullableTimestamps();

            $table->foreign('handover_note_id')->references('id')->on('exit_handover_notes');
            $table->foreign('project_code_id')->references('id')->on('lkup_project_codes');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('exit_handover_projects');
    }
};
