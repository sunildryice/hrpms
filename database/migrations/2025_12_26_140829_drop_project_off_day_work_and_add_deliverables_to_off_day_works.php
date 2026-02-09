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

//        Schema::dropIfExists('project_off_day_work');
//        Schema::dropIfExists('project_work_from_home');

        Schema::table('off_day_works', function (Blueprint $table) {
            $table->json('deliverables')->nullable()->after('reason');
        });

        Schema::table('work_from_homes', function (Blueprint $table) {
            $table->json('deliverables')->nullable()->after('reason');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('off_day_works', function (Blueprint $table) {
            $table->dropColumn('deliverables');
        });

        Schema::table('work_from_homes', function (Blueprint $table) {
            $table->dropColumn('deliverables');
        });

//        Schema::create('project_off_day_work', function (Blueprint $table) {
//            $table->id();
//            $table->unsignedBigInteger('off_day_work_id');
//            $table->unsignedBigInteger('project_id');
//            $table->json('deliverables')->nullable();
//            $table->timestamps();
//        });

//        Schema::create('work_from_homes', function (Blueprint $table) {
//            $table->id();
//            $table->unsignedBigInteger('off_day_work_id');
//            $table->unsignedBigInteger('project_id');
//            $table->json('deliverables')->nullable();
//            $table->timestamps();
//        });
    }
};
