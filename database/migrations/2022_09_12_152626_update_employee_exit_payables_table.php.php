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
        Schema::table('employee_exit_payables', function (Blueprint $table) {
            $table->decimal('deduction_amount', 12, 2)->nullable()->default(null)->after('other_payable_amount');
            $table->text('remarks')->nullable()->default(null)->after('deduction_amount');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('employee_exit_payables', function (Blueprint $table) {
            $table->dropColumn('remarks');
            $table->dropColumn('deduction_amount');
        });
    }
};
