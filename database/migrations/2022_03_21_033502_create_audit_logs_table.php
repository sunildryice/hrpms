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
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('original_user_id')->nullable()->default(null);
            $table->string('model')->nullable()->default(null);
            $table->integer('model_id')->unsigned()->nullable()->default(null);
            $table->string('action')->nullable()->default(null);
            $table->string('description')->nullable()->default(null);
            $table->text('before_details')->nullable()->default(null);
            $table->text('after_details')->nullable()->default(null);
            $table->ipAddress('ip_address')->nullable()->default(null);
            $table->nullableTimestamps();

            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('audit_logs');
    }
};
