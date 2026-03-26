<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('performance_professional_development_plans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('performance_review_id')->nullable();
            $table->longText('objective')->nullable();
            $table->longText('activity')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->nullableTimestamps();

            $table->foreign('performance_review_id', 'ppdp_review_id_fk')->references('id')->on('performance_reviews');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('performance_professional_development_plans');
    }
};
