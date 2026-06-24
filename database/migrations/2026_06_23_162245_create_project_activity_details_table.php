<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('project_activity_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_activity_id');
            $table->text('key_accomplishment')->nullable();
            $table->text('challenge')->nullable();
            $table->text('lesson_learned')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->nullableTimestamps();

            $table->foreign('project_activity_id')->references('id')->on('project_activities')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('project_activity_details');
    }
};
