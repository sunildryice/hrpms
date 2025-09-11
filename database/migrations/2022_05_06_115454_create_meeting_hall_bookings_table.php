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
        Schema::create('meeting_hall_bookings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('meeting_hall_id');
            $table->date('meeting_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->text('purpose')->nullable()->default(null);
            $table->unsignedTinyInteger('number_of_attendees')->nullable()->default(null);
            $table->text('remarks')->nullable()->default(null);
            $table->unsignedBigInteger('status_id')->nullable()->default(null);
            $table->unsignedBigInteger('created_by')->nullable()->default(null);
            $table->unsignedBigInteger('updated_by')->nullable()->default(null);
            $table->nullableTimestamps();

            $table->foreign('meeting_hall_id')->references('id')->on('lkup_meeting_halls');
            $table->foreign('created_by')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('meeting_hall_bookings');
    }
};
