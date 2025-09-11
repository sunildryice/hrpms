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
        Schema::create('local_travel_reimbursement_itineraries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('local_travel_reimbursement_id');
            $table->unsignedBigInteger('donor_code_id')->nullable()->default(null);
            $table->unsignedBigInteger('account_code_id')->nullable()->default(null);
            $table->unsignedBigInteger('activity_code_id')->nullable()->default(null);
            $table->string('travel_mode')->nullable()->default(null);
            $table->date('travel_date')->nullable()->default(null);
            $table->string('purpose')->nullable()->default(null);
            $table->string('departure_place')->nullable()->default(null);
            $table->string('arrival_place')->nullable()->default(null);
            $table->decimal('total_distance', 10, 2)->nullable()->default(null);
            $table->decimal('total_fare', 10, 2)->nullable()->default(null);
            $table->text('remarks')->nullable()->default(null);
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by')->nullable()->default(null);
            $table->nullableTimestamps();

            $table->foreign('local_travel_reimbursement_id', 'local_itinerary_local_travel_foreign')->references('id')->on('local_travel_reimbursements');
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('activity_code_id')->references('id')->on('lkup_activity_codes');
            $table->foreign('account_code_id')->references('id')->on('lkup_account_codes');
            $table->foreign('donor_code_id')->references('id')->on('lkup_donor_codes');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('local_travel_reimbursement_itineraries');
    }
};
