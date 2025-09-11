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
        Schema::create('mfr_agreement_amendments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('mfr_agreement_id');
            $table->date('effective_date')->nullable()->default(null);
            $table->date('extension_to_date')->nullable()->default(null);
            // $table->decimal('approved_budget')->nullable()->default(null);
            $table->decimal('approved_budget', 12, 2)->nullable();
            $table->longText('attachment')->nullable()->default(null);
            $table->unsignedBigInteger('created_by')->nullable()->default(null);
            $table->nullableTimestamps();

            $table->foreign('mfr_agreement_id')->references('id')->on('mfr_agreements');
            $table->foreign('created_by')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mfr_agreement_amendments');
    }
};
