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
        Schema::create('fund_request_activities', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('fund_request_id');
            $table->unsignedBigInteger('activity_code_id')->nullable()->default(null);
            $table->decimal('estimated_amount', 15,2)->default(0);
            $table->decimal('budget_amount', 15,2)->default(0);
            $table->decimal('required_amount', 15,2)->default(0);
            $table->string('project_target_unit')->nullable()->default(null);
            $table->string('dip_target_unit')->nullable()->default(null);
            $table->text('justification_note')->nullable()->default(null);
            $table->nullableTimestamps();

            $table->foreign('fund_request_id')->references('id')->on('fund_requests');
            $table->foreign('activity_code_id')->references('id')->on('lkup_activity_codes');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('fund_request_activities');
    }
};
