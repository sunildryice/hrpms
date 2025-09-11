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
        Schema::create('construction_installments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('construction_id');
            $table->date('advance_release_date')->nullable()->default(null);
            $table->unsignedBigInteger('transaction_type_id')->nullable()->default(null);
            $table->unsignedTinyInteger('installment_number')->default(1);
            $table->decimal('amount', 12, 2)->nullable()->default(null);
            $table->text('remarks')->nullable()->default(null);
            $table->unsignedBigInteger('reviewer_id')->nullable()->default(null);
            $table->unsignedBigInteger('approver_id')->nullable()->default(null);
            $table->unsignedBigInteger('status_id')->nullable()->default(null);
            $table->unsignedBigInteger('created_by')->nullable()->default(null);
            $table->unsignedBigInteger('updated_by')->nullable()->default(null);
            $table->nullableTimestamps();

            $table->foreign('construction_id')->references('id')->on('constructions');
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('reviewer_id')->references('id')->on('users');
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
        Schema::dropIfExists('construction_installments');
    }
};
