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
        Schema::table('local_travel_reimbursements', function (Blueprint $table) {
            $table->unsignedBigInteger('employee_id')->nullable()->after('travel_request_id');
            $table->foreign('employee_id')->references('id')->on('employees');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('local_travel_reimbursements', function (Blueprint $table) {
            $table->dropForeign('local_travel_reimbursements_employee_id_foreign');
            $table->dropColumn('employee_id');
        });
    }
};
