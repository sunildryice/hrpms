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
        Schema::create('travel_claim_expenses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('travel_claim_id');
            $table->unsignedBigInteger('activity_code_id');
            $table->date('expense_date')->nullable()->default(null);
            $table->text('expense_description')->nullable()->default(null);
            $table->decimal('expense_amount', 12, 2)->nullable()->default(null);
            $table->string('attachment')->nullable()->default(null);
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by')->nullable()->default(null);
            $table->nullableTimestamps();

            $table->foreign('travel_claim_id')->references('id')->on('travel_claims');
            $table->foreign('activity_code_id')->references('id')->on('lkup_activity_codes');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('travel_claim_expenses');
    }
};
