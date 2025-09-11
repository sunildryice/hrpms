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
        Schema::create('contracts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('supplier_id');
            $table->string('contract_number')->nullable()->default(null);
            $table->text('description')->nullable()->default(null);
            $table->string('contact_name')->nullable()->default(null);
            $table->string('contact_number')->nullable()->default(null);
            $table->string('address')->nullable()->default(null);
            $table->date('contract_date');
            $table->date('effective_date');
            $table->date('expiry_date');
            $table->unsignedTinyInteger('reminder_days')->nullable()->default(null);
            $table->unsignedTinyInteger('termination_days')->nullable()->default(null);
            $table->decimal('contract_amount', 12, 2)->nullable()->default(null);
            $table->unsignedBigInteger('focal_person_id')->nullable()->default(null);
            $table->string('attachment')->nullable()->default(null);
            $table->text('remarks')->nullable()->default(null);
            $table->unsignedBigInteger('created_by')->nullable()->default(null);
            $table->unsignedBigInteger('updated_by')->nullable()->default(null);
            $table->nullableTimestamps();

            $table->foreign('supplier_id')->references('id')->on('suppliers');
            $table->foreign('focal_person_id')->references('id')->on('employees');
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contracts');
    }
};
