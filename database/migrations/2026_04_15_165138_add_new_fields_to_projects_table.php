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
        Schema::table('projects', function (Blueprint $table) {
            $table->string('primary_funder')->nullable()->after('show_pms_dashboard');
            $table->string('contracting_agency')->nullable()->after('primary_funder');
            $table->decimal('budget_usd', 15, 2)->nullable()->after('contracting_agency');
            $table->json('district_ids')->nullable()->after('budget_usd');
            $table->unsignedBigInteger('project_theme_id')->nullable()->after('district_ids');
            $table->json('approach_ids')->nullable()->after('project_theme_id');
            $table->unsignedBigInteger('sector_id')->nullable()->after('approach_ids');

            $table->foreign('project_theme_id')->references('id')->on('lkup_project_themes');
            $table->foreign('sector_id')->references('id')->on('lkup_sectors');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropForeign(['project_theme_id']);
            $table->dropForeign(['sector_id']);

            $table->dropColumn('primary_funder');
            $table->dropColumn('contracting_agency');
            $table->dropColumn('budget_usd');
            $table->dropColumn('district_ids');
            $table->dropColumn('project_theme_id');
            $table->dropColumn('approach_ids');
            $table->dropColumn('sector_id');
        });
    }
};
