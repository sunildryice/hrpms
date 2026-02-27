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
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('inventory_item_id');
            $table->string('prefix')->nullable()->default(null);
            $table->year('year')->nullable()->default(null);
            $table->string('fiscal_year')->nullable()->default(null);
            $table->unsignedInteger('asset_number')->nullable()->default(null);
            $table->unsignedBigInteger('assigned_office_id')->nullable()->default(null);
            $table->unsignedBigInteger('assigned_department_id')->nullable()->default(null);
            $table->unsignedBigInteger('assigned_user_id')->nullable()->default(null);
            $table->string('serial_number')->nullable()->default(null);
            $table->bigInteger('condition_id')->nullable()->default(null);
            $table->longText('remarks')->nullable()->default(null);
            $table->unsignedTinyInteger('status')->default(1);
            $table->unsignedBigInteger('created_by')->nullable()->default(null);
            $table->unsignedBigInteger('updated_by')->nullable()->default(null);
            $table->nullableTimestamps();

            $table->foreign('inventory_item_id')->references('id')->on('inventory_items')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('assets');
    }
};
