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
        Schema::create('travel_request_accompaniments', function (Blueprint $table) {
            $table->unsignedBigInteger('travel_request_id');
            $table->unsignedBigInteger('employee_id');

            $table->foreign('travel_request_id')
                ->references('id')
                ->on('travel_requests')
                ->onDelete('cascade');

            $table->foreign('employee_id')
                ->references('id')
                ->on('employees')
                ->onDelete('cascade');

            $table->primary(['travel_request_id','employee_id'], 'travel_employee_primary');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('travel_request_accompaniments');
    }
};
