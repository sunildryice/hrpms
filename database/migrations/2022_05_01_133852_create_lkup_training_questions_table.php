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
        Schema::create('lkup_training_questions', function (Blueprint $table) {
            $table->id();
            $table->string('question')->unique();
            $table->enum('type', [1,3,6])->default(1);
            $table->enum('answer_type', ['textarea','boolean'])->default('textarea');
            $table->dateTime('activated_at')->nullable()->default(null);
            $table->unsignedMediumInteger('position')->default(1);
            $table->unsignedBigInteger('created_by')->nullable()->default(null);
            $table->unsignedBigInteger('updated_by')->nullable()->default(null);
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
        Schema::dropIfExists('lkup_training_questions');
    }
};
