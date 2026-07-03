<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hr_events', function (Blueprint $table) {
            $table->unsignedInteger('total_recruited')->nullable()->after('female_shortlisted');
            $table->text('remarks')->nullable()->after('female_participants');
        });

        Schema::table('hr_events', function (Blueprint $table) {
            $table->dropColumn('male_recruited');
            $table->dropColumn('female_recruited');
        });

        Schema::create('hr_event_recruitments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('hr_event_id');
            $table->string('member_name');
            $table->string('gender');
            $table->date('onboard_date')->nullable();
            $table->string('position')->nullable();
            $table->nullableTimestamps();

            $table->foreign('hr_event_id', 'fk_hr_evt_rec_hr_event')->references('id')->on('hr_events')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hr_event_recruitments');

        Schema::table('hr_events', function (Blueprint $table) {
            $table->unsignedInteger('male_recruited')->default(0);
            $table->unsignedInteger('female_recruited')->default(0);
        });

        Schema::table('hr_events', function (Blueprint $table) {
            $table->dropColumn('total_recruited');
            $table->dropColumn('remarks');
        });
    }
};
