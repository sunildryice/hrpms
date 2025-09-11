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
        Schema::create('construction_donors', function (Blueprint $table) {
            $table->unsignedBigInteger('construction_id')->required();
            $table->unsignedBigInteger('donor_code_id')->required();
            $table->foreign('construction_id')->references('id')->on('constructions')->cascadeOnDelete();
            $table->foreign('donor_code_id')->references('id')->on('lkup_donor_codes')->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('construction_donors');
    }
};
