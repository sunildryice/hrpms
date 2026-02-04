<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('local_travel_reimbursements', function (Blueprint $table) {
            $table->dropForeign('fk_local_travel_reimbursements_project_code');

            $table->foreign('project_code_id')->references('id')->on('projects');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('local_travel_reimbursements', function (Blueprint $table) {
            $table->dropForeign(['project_code_id']);

            $table->foreign('project_code_id')->references('id')->on('lkup_project_codes');
        });
    }
};
