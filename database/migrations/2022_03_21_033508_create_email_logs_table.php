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
        Schema::create('email_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable()->default(null);
            $table->ipAddress('ip_address')->nullable()->default(null);
            $table->text('subject')->nullable()->default(null);
            $table->longText('email_content')->nullable()->default(null);
            $table->text('from_email')->nullable()->default(null);
            $table->text('to_email')->nullable()->default(null);
            $table->text('cc_email')->nullable()->default(null);
            $table->string('type')->nullable()->default(null);
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
        Schema::dropIfExists('email_logs');
    }
};
