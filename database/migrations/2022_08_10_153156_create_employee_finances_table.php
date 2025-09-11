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
        Schema::create('employee_finances', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->string('ssf_number')->nullable()->default(null);
            $table->string('cit_number')->nullable()->default(null);
            $table->string('pf_number')->nullable()->default(null);
            $table->string('account_number')->nullable()->default(null);
            $table->string('bank_name')->nullable()->default(null);
            $table->string('branch_name')->nullable()->default(null);
            $table->string('remote_category')->nullable()->default(null);
            $table->boolean('disabled')->default(0);
            $table->unsignedBigInteger('created_by')->nullable()->default(null);
            $table->unsignedBigInteger('updated_by')->nullable()->default(null);
            $table->nullableTimestamps();

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
        Schema::dropIfExists('employee_finances');
    }
};
