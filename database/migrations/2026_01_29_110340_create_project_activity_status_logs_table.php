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
        Schema::create('project_activity_status_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_activity_id');
            $table->string('old_status');
            $table->string('new_status');
            $table->unsignedBigInteger('changed_by');
            $table->foreign('changed_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('project_activity_id')->references('id')->on('project_activities')->onDelete('cascade');
            $table->text('remarks')->nullable();
            $table->string('attachment')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('project_activity_status_logs');
    }
};
