<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hr_events', function (Blueprint $table) {
            $table->enum('recruitment_type', ['Direct Hire', 'Open Vacancy'])->nullable()->after('event_type');
            $table->enum('recruitment_method', ['Open Call', 'Headhunt', 'Direct Appointment'])->nullable()->after('recruitment_type');
        });
    }

    public function down(): void
    {
        Schema::table('hr_events', function (Blueprint $table) {
            $table->dropColumn('recruitment_type');
            $table->dropColumn('recruitment_method');
        });
    }
};
