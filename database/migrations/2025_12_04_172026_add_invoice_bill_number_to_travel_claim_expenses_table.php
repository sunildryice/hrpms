<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('travel_claim_expenses', function (Blueprint $table) {
            $table->string('invoice_bill_number', 100)
                ->nullable()
                ->after('expense_amount')
                ->comment('Invoice / Bill Number')
                ->index();
        });
    }

    public function down(): void
    {
        Schema::table('travel_claim_expenses', function (Blueprint $table) {
            $table->dropIndex(['invoice_bill_number']);
            $table->dropColumn('invoice_bill_number');
        });
    }
};