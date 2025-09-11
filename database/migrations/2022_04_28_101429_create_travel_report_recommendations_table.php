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
        Schema::create('travel_report_recommendations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('travel_report_id');
            $table->string('recommendation_subject')->nullable()->default(null);
            $table->string('recommendation_date')->nullable()->default(null);
            $table->string('recommendation_responsible')->nullable()->default(null);
            $table->text('recommendation_remarks')->nullable()->default(null);
            $table->nullableTimestamps();

            $table->foreign('travel_report_id')->references('id')->on('travel_reports');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('travel_report_recommendations');
    }
};
