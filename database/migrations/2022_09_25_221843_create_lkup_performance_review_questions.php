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
        Schema::create('lkup_performance_review_questions', function (Blueprint $table) {
            $table->id();
            $table->text('question')->nullable();
            $table->enum('answer_type', ['textarea', 'boolean'])->nullable();
            $table->longText('description')->nullable();
            $table->date('activated_at')->nullable();
            $table->integer('position')->nullable();
            $table->enum('group', ['A','B','C','D','E','F','G','H','I','J'])->nullable();
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->nullableTimestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lkup_performance_review_questions');
    }
};
