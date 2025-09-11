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
        Schema::table('payment_sheets', function (Blueprint $table) {
            $table->decimal('deduction_amount', 12, 2)->nullable()->default(0)->change();
        });

        Schema::table('construction_attachments', function (Blueprint $table) {
            $table->longText('link')->nullable()->default(null)->after('attachment');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('construction_attachments', function (Blueprint $table) {
            $table->dropColumn('link');
        });
    }
};
