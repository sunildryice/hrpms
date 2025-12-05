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
        Schema::create('travel_dsa_claim', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('travel_claim_id');

            $table->text('activities')->nullable()->default(null);

            $table->date('departure_date')->nullable()->default(null);
            $table->date('arrival_date')->nullable()->default(null);
            $table->string('departure_place')->nullable()->default(null);
            $table->string('arrival_place')->nullable()->default(null);

            $table->unsignedMediumInteger('days_spent')->nullable()->default(null);
            $table->decimal('breakfast', 10, 2)->nullable()->default(null);
            $table->decimal('lunch', 10, 2)->nullable()->default(null);
            $table->decimal('dinner', 10, 2)->nullable()->default(null);
            $table->decimal('incident_cost', 10, 2)->nullable()->default(null);
            $table->decimal('total_dsa', 10, 2)->nullable()->default(null);

            $table->decimal('daily_allowance', 10, 2)->nullable()->default(null);
            $table->decimal('lodging_expense', 10, 2)->nullable()->default(null);
            $table->decimal('other_expense', 10, 2)->nullable()->default(null);
            $table->decimal('total_amount', 10, 2)->nullable()->default(null);

            $table->text('remarks')->nullable()->default(null);
            $table->nullableTimestamps();

            $table->foreign('travel_claim_id')->references('id')->on('travel_claims');
        });

        Schema::create('travel_dsa_claim_modes', function (Blueprint $table) {
            $table->unsignedBigInteger('travel_dsa_claim_id');
            $table->unsignedBigInteger('travel_mode_id');

            $table->foreign('travel_dsa_claim_id')->references('id')->on('travel_dsa_claim')->onDelete('cascade');
            $table->foreign('travel_mode_id')->references('id')->on('lkup_travel_modes');

            $table->primary(['travel_dsa_claim_id', 'travel_mode_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('travel_dsa_claim');
        Schema::dropIfExists('travel_dsa_claim_modes');
    }
};
