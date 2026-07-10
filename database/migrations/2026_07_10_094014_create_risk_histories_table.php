<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('risk_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('risk_id');
            $table->date('updated_date')->nullable()->default(null);
            $table->unsignedBigInteger('risk_status_id')->nullable()->default(null);
            $table->text('description_of_risk')->nullable()->default(null);
            $table->text('mitigating_action')->nullable()->default(null);
            $table->text('whats_changed_this_period')->nullable()->default(null);
            $table->text('remarks')->nullable()->default(null);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->nullableTimestamps();

            $table->foreign('risk_id')->references('id')->on('risks')->cascadeOnDelete();
            $table->foreign('risk_status_id')->references('id')->on('lkup_risk_status')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('risk_histories');
    }
};
