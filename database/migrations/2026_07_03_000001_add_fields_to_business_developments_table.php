<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('business_developments', function (Blueprint $table) {
            $table->renameColumn('donor_name', 'funding_agency');
        });

        Schema::table('business_developments', function (Blueprint $table) {
            $table->string('project_name')->nullable()->after('thematic_area_id');
            $table->string('contracting_agency')->nullable()->after('funding_agency');
            $table->enum('partnership_type', ['Consortium', 'Single Organization'])->nullable()->after('contracting_agency');
            $table->string('consortium_lead')->nullable()->after('partnership_type');
            $table->string('consortium_partners')->nullable()->after('consortium_lead');
        });
    }

    public function down(): void
    {
        Schema::table('business_developments', function (Blueprint $table) {
            $table->dropColumn(['project_name', 'contracting_agency', 'partnership_type', 'consortium_lead', 'consortium_partners']);
        });

        Schema::table('business_developments', function (Blueprint $table) {
            $table->renameColumn('funding_agency', 'donor_name');
        });
    }
};
