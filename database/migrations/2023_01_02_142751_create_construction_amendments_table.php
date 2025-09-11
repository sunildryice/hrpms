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
        Schema::create('construction_amendments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('construction_id')->nullable()->default(null);
            $table->date('effective_date')->nullable()->default(null);
            $table->date('extension_to_date')->nullable()->default(null);
            $table->decimal('total_estimate_cost')->nullable()->default(null);
            $table->longText('attachment')->nullable()->default(null);
            $table->unsignedBigInteger('created_by')->nullable()->default(null);
            $table->foreign('construction_id')->references('id')->on('constructions');
            $table->foreign('created_by')->references('id')->on('users');
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
        Schema::dropIfExists('construction_amendments');
    }
};
