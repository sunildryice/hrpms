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
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('supplier_type')->default(1)->comment="1-Organization,2-Individual";
            $table->string('supplier_name')->nullable()->default(null);
            $table->string('address1')->nullable()->default(null);
            $table->string('address2')->nullable()->default(null);
            $table->string('contact_number')->nullable()->default(null);
            $table->string('email_address')->nullable()->default(null);
            $table->string('contact_person_name')->nullable()->default(null);
            $table->string('contact_person_email_address')->nullable()->default(null);
            $table->string('vat_pan_number')->nullable()->default(null);
            $table->text('remarks')->nullable()->default(null);
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
        Schema::dropIfExists('suppliers');
    }
};
