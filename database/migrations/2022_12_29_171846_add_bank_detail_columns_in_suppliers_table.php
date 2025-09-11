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
        Schema::table('suppliers', function (Blueprint $table) {
            $table->text('account_number')->default(null)->nullable()->after('vat_pan_number');
            $table->text('account_name')->default(null)->nullable()->after('account_number');
            $table->text('bank_name')->default(null)->nullable()->after('account_name');
            $table->text('branch_name')->default(null)->nullable()->after('bank_name');
            $table->text('swift_code')->default(null)->nullable()->after('branch_name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('suppliers', function (Blueprint $table) {
            $table->dropColumn('account_number');
            $table->dropColumn('account_name');
            $table->dropColumn('bank_name');
            $table->dropColumn('branch_name');
            $table->dropColumn('swift_code');
        });
    }
};
