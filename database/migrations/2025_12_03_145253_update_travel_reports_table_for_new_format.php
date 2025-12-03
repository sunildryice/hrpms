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
        Schema::table('travel_reports', function (Blueprint $table) {
            if (Schema::hasColumn('travel_reports', 'observation')) {
                $table->renameColumn('observation', 'major_achievement');
            }

            if (Schema::hasColumn('travel_reports', 'other_comments')) {
                $table->renameColumn('other_comments', 'conclusion_recommendations');
            }

            if (Schema::hasColumn('travel_reports', 'activities')) {
                $table->renameColumn('activities', 'not_completed_activities');
            }

            if (!Schema::hasColumn('travel_reports', 'total_travel_days')) {
                $table->unsignedInteger('total_travel_days')
                    ->nullable()
                    ->after('status_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('travel_reports', function (Blueprint $table) {
            if (Schema::hasColumn('travel_reports', 'major_achievement')) {
                $table->renameColumn('major_achievement', 'observation');
            }

            if (Schema::hasColumn('travel_reports', 'conclusion_recommendations')) {
                $table->renameColumn('conclusion_recommendations', 'other_comments');
            }

            if (Schema::hasColumn('travel_reports', 'not_completed_activities')) {
                $table->renameColumn('not_completed_activities', 'activities');
            }

            $table->dropColumnIfExists('total_travel_days');
        });
    }
};