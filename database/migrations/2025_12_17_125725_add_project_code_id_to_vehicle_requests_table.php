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
        Schema::table('vehicle_requests', function (Blueprint $table) {
            $table->unsignedBigInteger('project_code_id')->nullable()->default(null)->after('vehicle_request_type_id');

            $table->foreign('project_code_id', 'fk_vehicle_requests_project_code')
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
        Schema::table('vehicle_requests', function (Blueprint $table) {
            $table->dropForeign(['fk_vehicle_requests_project_code']);
            $table->dropColumn('project_code_id');
        });
    }
};
