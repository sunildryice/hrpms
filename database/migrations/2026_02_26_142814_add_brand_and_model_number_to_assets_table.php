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
        Schema::table('assets', function (Blueprint $table) {
            $table->unsignedBigInteger('brand_id')->nullable()->after('serial_number');
            $table->string('model_number')->nullable()->after('brand_id');

            $table->foreign('brand_id')->references('id')->on('lkup_brands');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->dropForeign(['brand_id']);
            $table->dropColumn(['brand_id', 'model_number']);
        });
    }
};
