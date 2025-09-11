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
        Schema::create('lkup_office_holidays', function (Blueprint $table) {
            $table->unsignedBigInteger('office_id');
            $table->unsignedBigInteger('holiday_id');

            $table->foreign('office_id')
                ->references('id')
                ->on('lkup_offices')
                ->onDelete('cascade');

            $table->foreign('holiday_id')
                ->references('id')
                ->on('lkup_holidays')
                ->onDelete('cascade');

            $table->primary(['office_id','holiday_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lkup_office_holidays');
    }
};
