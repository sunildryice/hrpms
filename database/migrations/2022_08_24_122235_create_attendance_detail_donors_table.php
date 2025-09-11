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
        Schema::create('attendance_detail_donors', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('attendance_detail_id');
            $table->unsignedBigInteger('donor_id');
            $table->double('worked_hours', 4, 2)->nullable()->default(null);
            $table->nullableTimestamps();

            $table->unique(['attendance_detail_id', 'donor_id']);
            $table->foreign('attendance_detail_id')
                ->references('id')
                ->on('attendance_details')
                ->onDelete('cascade');
            $table->foreign('donor_id')
                ->references('id')
                ->on('lkup_donor_codes');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('attendance_detail_donors');
    }
};
