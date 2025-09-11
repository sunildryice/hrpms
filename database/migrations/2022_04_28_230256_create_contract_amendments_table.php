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
        Schema::create('contract_amendments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('contract_id');
            $table->unsignedTinyInteger('amendment_number');
            $table->date('contract_date')->nullable()->default(null);
            $table->date('expiry_date');
            $table->decimal('contract_amount', 12, 2)->nullable()->default(null);
            $table->text('remarks')->nullable()->default(null);
            $table->string('attachment')->nullable()->default(null);
            $table->nullableTimestamps();

            $table->foreign('contract_id')->references('id')->on('contracts');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contract_amendments');
    }
};
