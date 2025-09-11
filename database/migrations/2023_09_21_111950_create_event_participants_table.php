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
        Schema::create('event_participants', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('event_completion_id');
            $table->string('name');
            $table->string('office')->nullable()->default(null);
            $table->string('designation')->nullable()->default(null);   
            $table->string('contact')->nullable()->default(null);
            $table->foreign('event_completion_id')->references('id')->on('event_completions');
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
        Schema::dropIfExists('event_participants');
    }
};
