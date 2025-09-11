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
        Schema::create('lkup_activity_account_codes', function (Blueprint $table) {
            $table->unsignedBigInteger('activity_code_id');
            $table->unsignedBigInteger('account_code_id');

            $table->foreign('activity_code_id')
                ->references('id')
                ->on('lkup_activity_codes')
                ->onDelete('cascade');

            $table->foreign('account_code_id')
                ->references('id')
                ->on('lkup_account_codes')
                ->onDelete('cascade');

            $table->primary(['activity_code_id','account_code_id'], 'activity_account_code_primary');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lkup_activity_account_codes');
    }
};
