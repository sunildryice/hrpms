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
        Schema::create('lkup_vehicles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('office_id')->nullable()->default(null);
            $table->unsignedBigInteger('vehicle_type_id')->nullable()->default(null);
            $table->string('vehicle_number')->nullable()->default(null);
            $table->unsignedTinyInteger('passenger_capacity')->nullable()->default(null);
            $table->dateTime('activated_at')->nullable()->default(null);
            $table->unsignedBigInteger('created_by')->nullable()->default(null);
            $table->unsignedBigInteger('updated_by')->nullable()->default(null);
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
        Schema::dropIfExists('lkup_vehicles');
    }
};
