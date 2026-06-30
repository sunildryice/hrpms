<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('risks', function (Blueprint $table) {
            $table->unsignedBigInteger('project_id')->nullable()->after('id');
            $table->foreign('project_id')->references('id')->on('projects');
        });

        Schema::table('risks', function (Blueprint $table) {
            $table->dropColumn('project');
        });

        Schema::table('hr_events', function (Blueprint $table) {
            $table->unsignedBigInteger('project_id')->nullable()->after('id');
            $table->foreign('project_id')->references('id')->on('projects');
        });

        Schema::table('hr_events', function (Blueprint $table) {
            $table->dropColumn('associated_project');
        });

        Schema::table('research_communication', function (Blueprint $table) {
            $table->unsignedBigInteger('project_id')->nullable()->after('id');
            $table->foreign('project_id')->references('id')->on('projects');
        });

        Schema::table('research_communication', function (Blueprint $table) {
            $table->dropColumn('associated_project');
        });
    }

    public function down(): void
    {
        Schema::table('research_communication', function (Blueprint $table) {
            $table->string('associated_project')->nullable();
        });

        Schema::table('research_communication', function (Blueprint $table) {
            $table->dropForeign(['project_id']);
            $table->dropColumn('project_id');
        });

        Schema::table('hr_events', function (Blueprint $table) {
            $table->string('associated_project')->nullable();
        });

        Schema::table('hr_events', function (Blueprint $table) {
            $table->dropForeign(['project_id']);
            $table->dropColumn('project_id');
        });

        Schema::table('risks', function (Blueprint $table) {
            $table->string('project')->nullable();
        });

        Schema::table('risks', function (Blueprint $table) {
            $table->dropForeign(['project_id']);
            $table->dropColumn('project_id');
        });
    }
};
