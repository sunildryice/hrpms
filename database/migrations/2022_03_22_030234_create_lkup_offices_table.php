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
        Schema::create('lkup_offices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('parent_id')->default(0);
            $table->unsignedBigInteger('district_id')->nullable()->default(null);
            $table->string('office_name');
            $table->string('office_code')->unique();
            $table->string('phone_number')->nullable()->default(null);
            $table->string('fax_number')->nullable()->default(null);
            $table->string('email_address')->nullable()->default(null);
            $table->string('account_number')->nullable()->default(null);
            $table->string('bank_name')->nullable()->default(null);
            $table->string('branch_name')->nullable()->default(null);
            $table->unsignedTinyInteger('weekend_type')->default(1);
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
        Schema::dropIfExists('lkup_offices');
    }
};
