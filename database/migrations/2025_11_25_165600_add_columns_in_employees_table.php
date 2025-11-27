<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('employees', function (Blueprint $table) {

            if (Schema::hasColumn('employees', 'telephone_number')) {
                $table->dropColumn('telephone_number');
            }

            if (!Schema::hasColumn('employees', 'nid_number')) {
                $table->string('nid_number', 50)->nullable()->after('pan_number');
            }

            if (!Schema::hasColumn('employees', 'passport_number')) {
                $table->string('passport_number', 50)->nullable()->after('nid_number');
            }
            if (!Schema::hasColumn('employees', 'passport_attachment')) {
                $table->string('passport_attachment')->nullable()->after('passport_number');
            }

            if (!Schema::hasColumn('employees', 'vehicle_license_number')) {
                $table->string('vehicle_license_number', 50)->nullable()->after('passport_attachment');
            }
            if (!Schema::hasColumn('employees', 'vehicle_license_category')) {
                $table->json('vehicle_license_category')->nullable()->after('vehicle_license_number');
            }

            if (!Schema::hasColumn('employees', 'cv_attachment')) {
                $table->string('cv_attachment')->nullable()->after('profile_picture');
            }

            if (!Schema::hasColumns('employees', ['bio'])) {
                $table->text('bio')->nullable()->after('cv_attachment');
            }
        });
    }

    public function down()
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn([
                'nid_number',
                'passport_number',
                'passport_attachment',
                'vehicle_license_number',
                'vehicle_license_category',
                'cv_attachment',
                'bio',
            ]);
        });
    }
};
