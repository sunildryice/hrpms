<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('travel_claim_local_travel', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('travel_claim_id');
            $table->text('purpose')->nullable()->default(null);
            $table->date('date')->nullable()->default(null);
            $table->string('departure_place')->nullable()->default(null);
            $table->string('arrival_place')->nullable()->default(null);
            $table->decimal('travel_fare', 10, 2)->nullable()->default(null);
            $table->text('remarks')->nullable()->default(null);
            $table->string('attachment')->nullable()->default(null);
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by')->nullable()->default(null);
            $table->nullableTimestamps();

            $table->foreign('travel_claim_id')->references('id')->on('travel_claims');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('travel_claim_local_travel');
    }
};
