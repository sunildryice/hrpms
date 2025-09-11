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
        Schema::create('lkup_bill_categories', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->dateTime('activated_at')->nullable()->default(null);
            $table->unsignedBigInteger('created_by')->nullable()->default(null);
            $table->unsignedBigInteger('updated_by')->nullable()->default(null);
            $table->nullableTimestamps();
        });

        Schema::table('payment_bills', function (Blueprint $table) {
            $table->unsignedBigInteger('category_id')->nullable()->default(null)->after('supplier_id');
            $table->unsignedBigInteger('office_id')->nullable()->default(null)->after('supplier_id');

            $table->foreign('category_id')->references('id')->on('lkup_bill_categories');
        });

        Schema::table('payment_sheet_details', function (Blueprint $table) {
            $table->unsignedBigInteger('tds_percentage')->nullable()->default(null)->after('total_amount');
            $table->unsignedBigInteger('vat_percentage')->nullable()->default(null)->after('total_amount');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('payment_bills', function (Blueprint $table) {
            $table->dropForeign('payment_bills_category_id_foreign');
            $table->dropColumn(['category_id', 'office_id']);
        });
        Schema::table('payment_sheet_details', function (Blueprint $table) {
            $table->dropColumn(['tds_percentage', 'vat_percentage']);
        });
        Schema::dropIfExists('lkup_bill_categories');
    }
};
