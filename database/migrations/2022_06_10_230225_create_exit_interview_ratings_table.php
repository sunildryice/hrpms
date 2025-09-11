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
        Schema::create('exit_interview_ratings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('exit_interview_id');
            $table->unsignedBigInteger('exit_rating_id');
            $table->boolean('excellent')->default(0);
            $table->boolean('good')->default(0);
            $table->boolean('fair')->default(0);
            $table->boolean('poor')->default(0);
            $table->nullableTimestamps();

            $table->foreign('exit_interview_id')->references('id')->on('exit_interviews');
            $table->foreign('exit_rating_id')->references('id')->on('lkup_exit_ratings');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('exit_interview_ratings');
    }
};
