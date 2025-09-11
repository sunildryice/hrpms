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
        Schema::create('probationary_review_indicators', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('probationary_review_id');
            $table->unsignedBigInteger('probationary_indicator_id');
            $table->boolean('improved_required')->default(0);
            $table->boolean('satisfactory')->default(0);
            $table->boolean('good')->default(0);
            $table->boolean('excellent')->default(0);
            $table->nullableTimestamps();

            $table->foreign('probationary_review_id')->references('id')->on('probationary_reviews');
            $table->foreign('probationary_indicator_id')->references('id')->on('lkup_probationary_indicators');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('probationary_review_indicators');
    }
};
