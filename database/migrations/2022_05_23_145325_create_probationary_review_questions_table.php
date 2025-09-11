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
        Schema::create('probationary_review_questions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('probationary_review_id');
            $table->unsignedBigInteger('question_id');
            $table->text('answer')->nullable()->default(null);
            $table->nullableTimestamps();

            $table->foreign('probationary_review_id')->references('id')->on('probationary_reviews');
            $table->foreign('question_id')->references('id')->on('lkup_probationary_questions');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('probationary_review_questions');
    }
};
