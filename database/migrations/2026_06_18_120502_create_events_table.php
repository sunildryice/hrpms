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
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_id');
            $table->enum('event_organized_by', ['internal', 'external'])->default('external');
            $table->enum('event_type', [
                'orientation',
                'meeting',
                'training',
                'workshop',
                'conference',
                'other',
            ])->nullable()->default(null);

            $table->string('event_name');
            $table->date('from_date');
            $table->date('to_date');
            $table->string('country')->nullable()->default(null);
            $table->string('province')->nullable()->default(null);
            $table->string('district')->nullable()->default(null);
            $table->string('city_local_level')->nullable()->default(null);
            $table->string('organized_by')->nullable()->default(null);
            $table->string('role')->nullable()->default(null);
            $table->unsignedInteger('total_participants_government')->nullable();
            $table->unsignedInteger('total_herdi_participants')->nullable();
            $table->unsignedInteger('total_other_participants')->nullable();
            $table->boolean('roaster_details')->default(false);
            $table->text('action_points')->nullable();
            $table->text('remarks')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->nullableTimestamps();

            $table->foreign('project_id')->references('id')->on('projects');
        });

        Schema::create('event_roasters', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('event_id');
            $table->enum('organisation', ['HERDi', 'Government', 'Other'])->default('Other');
            $table->string('organisation_name')->nullable()->default(null);
            $table->string('position')->nullable();
            $table->string('ethnicity')->nullable();
            $table->enum('gender', ['Male', 'Female', 'Other'])->nullable()->default(null);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->nullableTimestamps();

            $table->foreign('event_id')->references('id')->on('events');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_roasters');
        Schema::dropIfExists('events');
    }
};
