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
        Schema::create('travel_authorization_officials', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('travel_authorization_id');
            $table->string('name')->nullable();
            $table->string('post')->nullable();
            $table->string('level')->nullable();
            $table->string('office')->nullable();
            $table->unsignedBigInteger('district_id')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

            $table->foreign('travel_authorization_id')->references('id')->on('travel_authorization_requests');
            $table->foreign('district_id')->references('id')->on('lkup_districts');
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
        Schema::dropIfExists('travel_authorization_officials');
    }
};
