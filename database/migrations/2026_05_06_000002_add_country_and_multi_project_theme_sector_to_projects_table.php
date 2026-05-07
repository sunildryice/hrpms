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

            $table->dropForeign('projects_project_theme_id_foreign');
            $table->dropForeign('projects_sector_id_foreign');

            $table->renameColumn('project_theme_id', 'project_theme_ids');
            $table->renameColumn('sector_id', 'sector_ids');

            $table->json('project_theme_ids')->nullable()->change();
            $table->json('sector_ids')->nullable()->change();

            $table->json('country_ids')->nullable()->after('district_ids');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {

            $table->dropColumn('country_ids');

            $table->unsignedBigInteger('project_theme_ids')->nullable()->change();
            $table->unsignedBigInteger('sector_ids')->nullable()->change();

            $table->renameColumn('project_theme_ids', 'project_theme_id');
            $table->renameColumn('sector_ids', 'sector_id');

            $table->foreign('project_theme_id')->references('id')->on('lkup_project_themes');
            $table->foreign('sector_id')->references('id')->on('lkup_sectors');
        });
    }
};
