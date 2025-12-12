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
        Schema::create('lieu_leave_balances', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('user_id');
            $table->date('earned_date');          // date of off-day work or approval
            $table->date('earned_month');

            $table->unsignedBigInteger('lieu_leave_request_id')->nullable(); // filled when used
            $table->unsignedBigInteger('off_day_work_id')->nullable(); // reference to off-day work entry
            $table->date('expires_at');           // earned_date + 30 days (or your rule)


            $table->foreign('user_id')
                ->references('id')->on('users')
                ->onDelete('cascade');

            $table->foreign('lieu_leave_request_id')
                ->references('id')->on('lieu_leave_requests')
                ->onDelete('set null');

            $table->foreign('off_day_work_id')
                ->references('id')->on('off_day_works')
                ->onDelete('set null');

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
        Schema::dropIfExists('lieu_leave_balances');
    }
};
