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
        Schema::create('payment_sheets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('fiscal_year_id')->nullable()->default(null);
            $table->unsignedBigInteger('supplier_id')->nullable()->default(null);
            $table->unsignedBigInteger('project_code_id')->nullable()->default(null);
            $table->unsignedBigInteger('office_id')->nullable()->default(null);
            $table->string('prefix')->nullable()->default(null);
            $table->unsignedInteger('sheet_number')->nullable()->default(null);
            $table->decimal('total_amount', 12, 2)->nullable()->default(null);
            $table->decimal('vat_amount', 12, 2)->nullable()->default(null);
            $table->decimal('tds_amount', 12, 2)->nullable()->default(null);
            $table->decimal('net_amount', 12, 2)->nullable()->default(null);
            $table->decimal('deduction_amount', 12, 2)->nullable()->default(0);
            $table->decimal('paid_amount', 12, 2)->nullable()->default(0);
            $table->longText('deduction_remarks')->nullable()->default(null);
            $table->string('voucher_reference_number')->nullable()->default(null);
            $table->longText('purpose')->nullable()->default(null);
            $table->unsignedBigInteger('verifier_id')->nullable()->default(null);
            $table->unsignedBigInteger('reviewer_id')->nullable()->default(null);
            $table->unsignedBigInteger('recommender_id')->nullable()->default(null);
            $table->unsignedBigInteger('approver_id')->nullable()->default(null);
            $table->unsignedBigInteger('status_id')->nullable()->default(null);
            $table->unsignedBigInteger('created_by')->nullable()->default(null);
            $table->unsignedBigInteger('updated_by')->nullable()->default(null);
            $table->nullableTimestamps();

            $table->foreign('fiscal_year_id')->references('id')->on('lkup_fiscal_years');
            $table->foreign('supplier_id')->references('id')->on('suppliers');
            $table->foreign('project_code_id')->references('id')->on('lkup_project_codes');
            $table->foreign('verifier_id')->references('id')->on('users');
            $table->foreign('reviewer_id')->references('id')->on('users');
            $table->foreign('recommender_id')->references('id')->on('users');
            $table->foreign('approver_id')->references('id')->on('users');
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
        Schema::dropIfExists('payment_sheets');
    }
};
