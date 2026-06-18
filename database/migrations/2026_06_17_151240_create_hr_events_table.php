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
        Schema::create('hr_events', function (Blueprint $table) {
            $table->id();
            $table->date('event_date');
            $table->enum('event_type', ['Recruitment', 'Orientation']);
            $table->string('vacancy_for_positions');
            $table->string('associated_project')->nullable();
            $table->unsignedInteger('total_applicants')->default(0);
            $table->unsignedInteger('male_shortlisted')->default(0);
            $table->unsignedInteger('female_shortlisted')->default(0);
            $table->unsignedInteger('male_recruited')->default(0);
            $table->unsignedInteger('female_recruited')->default(0);

            $table->string('orientation_title')->nullable();
            $table->unsignedInteger('male_participants')->default(0);
            $table->unsignedInteger('female_participants')->default(0);
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
        Schema::dropIfExists('hr_events');
    }
};
