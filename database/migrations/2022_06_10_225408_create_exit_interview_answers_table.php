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
        Schema::create('exit_interview_answers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('exit_interview_id');
            $table->unsignedBigInteger('question_id');
            $table->text('answer')->nullable()->default(null);
            $table->nullableTimestamps();

            $table->foreign('exit_interview_id')->references('id')->on('exit_interviews');
            $table->foreign('question_id')->references('id')->on('lkup_exit_questions');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('exit_interview_answers');
    }
};
