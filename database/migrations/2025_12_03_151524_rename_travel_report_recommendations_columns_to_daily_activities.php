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
        Schema::table('travel_report_recommendations', function (Blueprint $table) {
            if (Schema::hasColumn('travel_report_recommendations', 'recommendation_date')) {
                $table->date('recommendation_date')->nullable()->change();
            }

            if (Schema::hasColumn('travel_report_recommendations', 'recommendation_subject')) {
                $table->text('recommendation_subject')->nullable()->change();
            }

            $table->renameColumn('recommendation_responsible', 'day_number');
            $table->renameColumn('recommendation_date', 'activity_date');
            $table->renameColumn('recommendation_subject', 'completed_tasks');
            $table->renameColumn('recommendation_remarks', 'remarks');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('travel_report_recommendations', function (Blueprint $table) {
            $table->renameColumn('day_number', 'recommendation_responsible');
            $table->renameColumn('activity_date', 'recommendation_date');
            $table->renameColumn('completed_tasks', 'recommendation_subject');
            $table->renameColumn('remarks', 'recommendation_remarks');

            $table->string('recommendation_date')->nullable()->change();
            $table->string('recommendation_subject')->nullable()->change();
        });
    }
};