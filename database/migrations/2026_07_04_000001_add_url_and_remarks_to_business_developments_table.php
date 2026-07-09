<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('business_developments', function (Blueprint $table) {
            $table->string('url', 500)->nullable()->after('call_name');
        });

        Schema::table('business_developments', function (Blueprint $table) {
            $table->text('remarks')->nullable()->after('result');
        });
    }

    public function down(): void
    {
        Schema::table('business_developments', function (Blueprint $table) {
            $table->dropColumn(['url', 'remarks']);
        });
    }
};
