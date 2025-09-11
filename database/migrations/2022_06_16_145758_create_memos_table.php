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
        Schema::create('memos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('fiscal_year_id')->nullable()->default(null);
            $table->unsignedBigInteger('office_id')->nullable()->default(null);
            $table->string('prefix')->nullable()->default(null);
            $table->unsignedInteger('memo_number')->nullable()->default(null);
            $table->date('memo_date')->nullable()->default(null);
            $table->string('subject')->nullable()->default(null);
            $table->longText('description')->nullable()->default(null);
            $table->text('enclosure')->nullable()->default(null);
            $table->string('attachment')->nullable()->default(null);
            $table->dateTime('submitted_at')->nullable()->default(null);
            $table->unsignedBigInteger('status_id')->nullable()->default(null);
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
        Schema::dropIfExists('memos');
    }
};
