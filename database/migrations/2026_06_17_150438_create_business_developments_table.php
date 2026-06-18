<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('lkup_thematic_areas', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->nullableTimestamps();
        });

        Schema::create('business_developments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('thematic_area_id')->nullable()->default(null);
            $table->date('date');
            $table->string('call_name')->nullable()->default(null);
            $table->string('donor_name')->nullable()->default(null);
            $table->enum('project_type', ['Research', 'Implementation'])->nullable()->default(null);
            $table->enum('status', ['Scanned', 'Submitted'])->nullable()->default(null);
            $table->enum('result', ['Rejected', 'Awaiting Result', 'Awarded'])->nullable()->default(null);
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
        Schema::dropIfExists('business_developments');
        Schema::dropIfExists('lkup_thematic_areas');
    }
};
