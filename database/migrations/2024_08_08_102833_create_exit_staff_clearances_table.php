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

        Schema::create('lkup_staff_clearance_departments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('parent_id')->default(0);
            $table->string('title');
            $table->dateTime('activated_at')->nullable();
            $table->nullableTimestamps();
        });

        Schema::create('exit_staff_clearances', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('handover_note_id');
            $table->unsignedBigInteger('supervisor_id')->nullable();
            $table->unsignedBigInteger('endorser_id')->nullable();
            $table->unsignedBigInteger('certifier_id')->nullable();
            $table->unsignedBigInteger('approver_id')->nullable();
            $table->dateTime('verified_at')->nullable();
            $table->dateTime('endorsed_at')->nullable();
            $table->dateTime('certified_at')->nullable();
            $table->dateTime('approved_at')->nullable();
            $table->text('remarks')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('status_id')->nullable();
            $table->nullableTimestamps();

            $table->foreign('handover_note_id')->references('id')->on('exit_handover_notes');
            $table->foreign('supervisor_id')->references('id')->on('users');
            $table->foreign('endorser_id')->references('id')->on('users');
            $table->foreign('certifier_id')->references('id')->on('users');
            $table->foreign('approver_id')->references('id')->on('users');
            $table->foreign('created_by')->references('id')->on('users');
        });

        Schema::create('exit_staff_clearance_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('staff_clearance_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('original_user_id')->nullable();
            $table->longText('log_remarks')->nullable();
            $table->unsignedBigInteger('status_id')->nullable();
            $table->nullableTimestamps();

            $table->foreign('staff_clearance_id')->references('id')->on('exit_staff_clearances');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lkup_staff_clearance_departments');
        Schema::dropIfExists('exit_staff_clearance_logs');
        Schema::dropIfExists('exit_staff_clearances');
    }
};
