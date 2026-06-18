<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('lkup_risk_status', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->nullableTimestamps();
        });

        Schema::create('lkup_risk_types', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->nullableTimestamps();
        });

        Schema::create('lkup_risk_probabilitis', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->nullableTimestamps();
        });

        Schema::create('lkup_risk_impacts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->nullableTimestamps();
        });
        Schema::create('lkup_risk_ratings', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->nullableTimestamps();
        });
        Schema::create('lkup_risk_response_types', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->nullableTimestamps();
        });

        Schema::create('risks', function (Blueprint $table) {
            $table->id();
            $table->string('project');
            $table->date('date_added')->comment('Date added');
            $table->string('risk_name');
            $table->unsignedBigInteger('risk_status_id')->nullable()->default(null);
            $table->unsignedBigInteger('risk_type_id')->nullable()->default(null);
            $table->unsignedBigInteger('risk_probability_id')->nullable()->default(null);
            $table->unsignedBigInteger('risk_impact_id')->nullable()->default(null)->comment('residual impact');
            $table->unsignedBigInteger('risk_rating_id')->nullable()->default(null);
            $table->unsignedBigInteger('risk_response_type_id')->nullable()->default(null);
            $table->text('description_of_risk')->nullable()->default(null);
            $table->string('risk_owner')->nullable()->default(null);
            $table->text('mitigating_action')->nullable()->default(null);
            $table->text('whats_changed_this_quarter')->nullable()->default(null);
            $table->text('remarks')->nullable()->default(null);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->nullableTimestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('risks');
        Schema::dropIfExists('lkup_risk_response_types');
        Schema::dropIfExists('lkup_risk_ratings');
        Schema::dropIfExists('lkup_risk_impacts');
        Schema::dropIfExists('lkup_risk_probabilitis');
        Schema::dropIfExists('lkup_risk_types');
        Schema::dropIfExists('lkup_risk_status');
    }
};
