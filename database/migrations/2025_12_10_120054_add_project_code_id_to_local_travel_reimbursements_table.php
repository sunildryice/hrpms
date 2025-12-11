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

            $table->unsignedBigInteger('project_code_id')->nullable()->default(null)->after('travel_request_id');

            $table->foreign('project_code_id', 'fk_local_travel_reimbursements_project_code')
                ->references('id')->on('lkup_project_codes');

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
            
            $table->dropForeign(['fk_local_travel_reimbursements_project_code']);
            $table->dropColumn('project_code_id');
            
        });
    }
};
