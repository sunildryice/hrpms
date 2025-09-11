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
        Schema::create('lkup_leave_types', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('fiscal_year_id')->nullable()->default(null);
            $table->string('title');
            $table->string('short_description')->nullable()->default(null);
            $table->unsignedTinyInteger('leave_frequency')->default(1)->comment('1=Yearly,2=Monthly');
            $table->unsignedTinyInteger('leave_basis')->default(1)->comment('1=Day,2=Hour');
            $table->unsignedMediumInteger('number_of_days')->default(0);
            $table->unsignedInteger('maximum_carry_over')->nullable()->default(null);
            $table->boolean('paid')->default(1);
            $table->boolean('default')->default(1);
            $table->boolean('include_weekends')->default(1);
            $table->boolean('female')->default(0);
            $table->boolean('male')->default(0);
            $table->boolean('applicable_to_all')->default(1);
            $table->boolean('encashment')->default(0);
            $table->dateTime('activated_at')->nullable()->default(null);
            $table->unsignedBigInteger('created_by')->nullable()->default(null);
            $table->unsignedBigInteger('updated_by')->nullable()->default(null);
            $table->nullableTimestamps();

            $table->foreign('fiscal_year_id')->references('id')->on('lkup_fiscal_years');
            $table->foreign('created_by')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lkup_leave_types');
    }
};
