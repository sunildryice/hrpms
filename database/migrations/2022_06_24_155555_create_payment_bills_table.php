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
        Schema::create('payment_bills', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('fiscal_year_id')->nullable()->default(null);
            $table->unsignedBigInteger('supplier_id')->nullable()->default(null);
            $table->string('bill_number')->nullable()->default(null);
            $table->date('bill_date')->nullable()->default(null);
            $table->text('remarks')->nullable()->default(null);
            $table->boolean('vat_flag')->default(0);
            $table->decimal('bill_amount', 12, 2)->nullable()->default(null);
            $table->decimal('vat_amount', 12, 2)->nullable()->default(null);
            $table->decimal('total_amount', 12, 2)->nullable()->default(null);
            $table->unsignedTinyInteger('paid_percentage')->default(0);
            $table->string('attachment')->nullable()->default(null);
            $table->unsignedBigInteger('created_by')->nullable()->default(null);
            $table->unsignedBigInteger('updated_by')->nullable()->default(null);
            $table->nullableTimestamps();

            $table->foreign('fiscal_year_id')->references('id')->on('lkup_fiscal_years');
            $table->foreign('supplier_id')->references('id')->on('suppliers');
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
        Schema::dropIfExists('payment_bills');
    }
};
