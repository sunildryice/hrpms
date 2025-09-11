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
        Schema::table('exit_handover_activities', function (Blueprint $table) {
            $table->unsignedBigInteger('activity_code_id')->nullable()->change();
            $table->mediumText('activity')->nullable()->after('handover_note_id');
        });

        Schema::table('exit_handover_projects', function (Blueprint $table) {
            $table->unsignedBigInteger('project_code_id')->nullable()->change();
            $table->mediumText('project')->nullable()->after('handover_note_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('exit_handover_activities', function (Blueprint $table) {
            $table->dropColumn(['activity']);
        });
        Schema::table('exit_handover_projects', function (Blueprint $table) {
            $table->dropColumn(['project']);
        });
    }
};
