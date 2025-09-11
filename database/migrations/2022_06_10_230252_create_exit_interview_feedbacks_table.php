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
        Schema::create('exit_interview_feedbacks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('exit_interview_id');
            $table->unsignedBigInteger('exit_feedback_id');
            $table->boolean('always')->default(0);
            $table->boolean('almost')->default(0);
            $table->boolean('usually')->default(0);
            $table->boolean('sometimes')->default(0);
            $table->nullableTimestamps();

            $table->foreign('exit_interview_id')->references('id')->on('exit_interviews');
            $table->foreign('exit_feedback_id')->references('id')->on('lkup_exit_feedbacks');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('exit_interview_feedbacks');
    }
};
