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
        Schema::create('performance_review_key_goals', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('performance_review_id')->nullable();
            $table->string('title', 1000)->nullable();
            $table->longText('description_employee')->nullable();
            $table->longText('description_supervisor')->nullable();
            $table->longText('description_employee_annual')->nullable();
            $table->longText('description_supervisor_annual')->nullable();
            $table->enum('type', ['current','future'])->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
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
        Schema::dropIfExists('performance_review_key_goals');
    }
};
