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
        Schema::create('purchase_request_packages', function (Blueprint $table) {
            $table->id();
            $table->string('package_name')->nullable()->default(null);
            $table->text('package_description')->nullable()->default(null);
            $table->decimal('total_amount', 15, 2)->nullable()->default(0);
            $table->dateTime('activated_at')->nullable()->default(null);
            $table->unsignedBigInteger('created_by')->nullable()->default(null);
            $table->unsignedBigInteger('updated_by')->nullable()->default(null);
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
        Schema::dropIfExists('purchase_request_packages');
    }
};
