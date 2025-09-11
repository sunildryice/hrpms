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
        Schema::table('employees', function (Blueprint $table) {
            $table->unsignedBigInteger('next_line_manager_id')->nullable()->default(null)->after('employee_code');
            $table->unsignedBigInteger('cross_supervisor_id')->nullable()->default(null)->after('employee_code');
            $table->unsignedBigInteger('supervisor_id')->nullable()->default(null)->after('employee_code');

            $table->foreign('supervisor_id')->references('id')->on('employees');
            $table->foreign('cross_supervisor_id')->references('id')->on('employees');
            $table->foreign('next_line_manager_id')->references('id')->on('employees');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropForeign('employees_supervisor_id_foreign');
            $table->dropForeign('employees_cross_supervisor_id_foreign');
            $table->dropForeign('employees_next_line_manager_id_foreign');
            $table->dropColumn('supervisor_id');
            $table->dropColumn('cross_supervisor_id');
            $table->dropColumn('next_line_manager_id');
        });
    }
};
