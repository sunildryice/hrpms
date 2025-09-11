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
        Schema::create('advance_settlement_expense_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('advance_settlement_id');
            $table->unsignedBigInteger('settlement_expense_id');
            $table->date('expense_date')->nullable()->nullable();
            $table->string('bill_number')->nullable()->nullable();
            $table->decimal('gross_amount', 12, 2)->nullable()->nullable();
            $table->decimal('tax_amount', 12, 2)->nullable()->nullable();
            $table->decimal('net_amount', 12, 2)->nullable()->nullable();
            $table->unsignedBigInteger('expense_category_id')->nullable()->default(null);
            $table->unsignedBigInteger('expense_type_id')->nullable()->default(null);
            $table->string('attachment')->nullable()->default(null);
            $table->unsignedBigInteger('created_by')->nullable()->default(null);
            $table->unsignedBigInteger('updated_by')->nullable()->default(null);
            $table->nullableTimestamps();

            $table->foreign('advance_settlement_id')->references('id')->on('advance_settlements');
            $table->foreign('settlement_expense_id')->references('id')->on('advance_settlement_expenses');
            $table->foreign('expense_category_id')->references('id')->on('lkup_expense_categories');
            $table->foreign('expense_type_id')->references('id')->on('lkup_expense_types');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('advance_settlement_expense_details');
    }
};
