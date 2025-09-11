<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('attendance_detail_donors', function (Blueprint $table) {
            $table->after('worked_hours', function (Blueprint $table) {
                $table->unsignedBigInteger('project_id')->nullable()->default(null);
                $table->text('activities')->nullable()->default(null);
            });

            $table->foreign('project_id')->references('id')->on('lkup_project_codes')->onDelete('restrict');
        });

        Schema::table('lkup_project_codes', function (Blueprint $table) {
            $table->after('title', function (Blueprint $table) {
                $table->string('short_name')->nullable()->default(null);
            });
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('attendance_detail_donors', function (Blueprint $table) {
            $table->dropForeign('attendance_detail_donors_project_id_foreign');
            $table->dropColumn(['project_id', 'activities']);
        });

        Schema::table('lkup_project_codes', function (Blueprint $table) {
            $table->dropColumn(['short_name']);
        });
    }
};
