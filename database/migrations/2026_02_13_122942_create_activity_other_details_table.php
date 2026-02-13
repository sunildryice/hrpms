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
        Schema::create('activity_other_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_activity_id');
            $table->string('key');
            $table->text('value')->nullable();
            $table->foreign('project_activity_id')->references('id')->on('project_activities')->onDelete('cascade');

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
        Schema::dropIfExists('activity_other_details');
    }
};
