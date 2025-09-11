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
        Schema::create('training_report_questions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('training_report_id');
            $table->unsignedBigInteger('question_id');
            $table->text('answer')->nullable()->default(null);
            $table->nullableTimestamps();

            $table->foreign('training_report_id')->references('id')->on('training_reports');
            $table->foreign('question_id')->references('id')->on('lkup_training_questions');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('training_report_questions');
    }
};
