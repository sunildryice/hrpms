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
        Schema::create('construction_parties', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('construction_id');
            $table->string('party_name')->nullable()->default(null);
            $table->decimal('contribution_amount', 12, 2)->default(0);
            $table->unsignedTinyInteger('contribution_percentage')->default(0);
            $table->nullableTimestamps();

            $table->foreign('construction_id')->references('id')->on('constructions');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('construction_parties');
    }
};
