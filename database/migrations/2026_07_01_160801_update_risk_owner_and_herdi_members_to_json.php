<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('risks', function (Blueprint $table) {
            $table->json('risk_owner')->nullable()->change();
        });

        Schema::table('research_communication', function (Blueprint $table) {
            $table->json('herdi_members_involved')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('risks', function (Blueprint $table) {
            $table->string('risk_owner', 255)->nullable()->change();
        });

        Schema::table('research_communication', function (Blueprint $table) {
            $table->string('herdi_members_involved', 255)->nullable()->change();
        });
    }
};
