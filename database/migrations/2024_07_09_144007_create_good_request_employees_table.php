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
        Schema::create('good_request_employees', function (Blueprint $table) {
            $table->unsignedBigInteger('good_request_id');
            $table->unsignedBigInteger('employee_id');

            $table->foreign('good_request_id')->references('id')->on('good_requests')->onDelete('cascade');
            $table->foreign('employee_id')->references('id')->on('employees');

            $table->primary(['good_request_id', 'employee_id'], 'pk_good_request_employee');
        });

        Schema::create('good_request_recipients', function (Blueprint $table) {
            $table->unsignedBigInteger('good_request_id');
            $table->string('name');
            $table->text('address')->nullable();
            $table->text('remarks')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->nullableTimestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('good_request_employees');
        Schema::dropIfExists('good_request_recipients');
    }
};
