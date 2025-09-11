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
        Schema::create('mfr_transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('mfr_agreement_id');
            $table->unsignedBigInteger('requester_id')->nullable();
            $table->unsignedBigInteger('reviewer_id')->nullable();
            $table->unsignedBigInteger('verifier_id')->nullable();
            $table->unsignedBigInteger('recommender_id')->nullable();
            $table->unsignedBigInteger('approver_id')->nullable();
            $table->unsignedTinyInteger('transaction_type')->nullable();
            $table->date('transaction_date')->nullable();
            $table->decimal('approved_budget', 12, 2)->nullable();
            $table->decimal('release_amount', 12, 2)->nullable();
            $table->decimal('expense_amount', 12, 2)->nullable();
            $table->decimal('reimbursed_amount', 12, 2)->nullable();
            $table->text('remarks')->nullable();
            $table->text('question_remarks')->nullable();
            $table->unsignedBigInteger('status_id')->nullable()->default(null);

            $table->nullableTimestamps();

            $table->foreign('mfr_agreement_id')->references('id')->on('mfr_agreements');
            $table->foreign('requester_id')->references('id')->on('users');
            $table->foreign('reviewer_id')->references('id')->on('users');
            $table->foreign('verifier_id')->references('id')->on('users');
            $table->foreign('recommender_id')->references('id')->on('users');
            $table->foreign('approver_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mfr_transactions');
    }
};
