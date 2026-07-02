<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('business_developments', function (Blueprint $table) {
            $table->string('attachment')->nullable()->after('result');
        });

        Schema::table('events', function (Blueprint $table) {
            $table->string('attachment')->nullable()->after('remarks');
        });
    }

    public function down(): void
    {
        Schema::table('business_developments', function (Blueprint $table) {
            $table->dropColumn('attachment');
        });

        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn('attachment');
        });
    }
};
