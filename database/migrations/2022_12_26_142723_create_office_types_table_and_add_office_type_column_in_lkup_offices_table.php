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
        Schema::create('lkup_office_types', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable()->default(null);
            $table->unsignedBigInteger('created_by')->nullable()->default(null);
            $table->unsignedBigInteger('updated_by')->nullable()->default(null);
            $table->timestamps();
        });

        Schema::table('lkup_offices', function (Blueprint $table) {
            $table->unsignedBigInteger('office_type_id')->nullable()->default(null)->after('parent_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('lkup_offices', function (Blueprint $table) {
            $table->dropForeign('lkup_offices_office_type_id_foreign');
            $table->dropColumn('office_type_id');
        }); 
        Schema::dropIfExists('lkup_office_types');
    }
};
