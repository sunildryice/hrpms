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
        Schema::create('leave_request_substitutes', function (Blueprint $table) {
            $table->unsignedBigInteger('leave_request_id');
            $table->unsignedBigInteger('substitute_id');

            $table->foreign('leave_request_id')->references('id')->on('leave_requests')->onDelete('cascade');
            $table->foreign('substitute_id')->references('id')->on('employees');

            $table->primary(['leave_request_id','substitute_id']);
        });

        Schema::table('leave_requests', function (Blueprint $table) {
            $table->string('attachment')->nullable()->default(null)->after('remarks');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('leave_request_substitutes');
        Schema::table('leave_requests', function (Blueprint $table) {
            $table->dropColumn('attachment');
        });
    }
};
