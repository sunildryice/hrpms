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
        Schema::create('lieu_leave_request_substitutes', function (Blueprint $table) {
            $table->unsignedBigInteger('lieu_leave_request_id');
            $table->unsignedBigInteger('substitute_id');

            $table->foreign('lieu_leave_request_id')->references('id')->on('lieu_leave_requests')->onDelete('cascade');
            $table->foreign('substitute_id')->references('id')->on('employees');

            $table->primary(['lieu_leave_request_id', 'substitute_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lieu_leave_request_substitutes');
    }
};
