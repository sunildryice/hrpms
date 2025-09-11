<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            ProvinceSeeder::class,
            DistrictSeeder::class,
            LocalLevelSeeder::class,
            PermissionSeeder::class,
            RoleSeeder::class,
            UserSeeder::class,

            BloodGroupSeeder::class,
            DepartmentSeeder::class,
            DesignationSeeder::class,
            EducationLevelSeeder::class,
            FamilyRelationSeeder::class,
            FiscalYearSeeder::class,
            GenderSeeder::class,
            MaritalStatusSeeder::class,
            MeetingHallSeeder::class,
            OfficeTypeSeeder::class,
            ProbationaryReviewTypeSeeder::class,
            StatusSeeder::class,

            LeaveModeSeeder::class,
            TravelModesSeeder::class,
            TravelTypeSeeder::class,

            VehicleTypeSeeder::class,
            VehicleRequestTypeSeeder::class,
            InventoryTypeSeeder::class,

            PerformanceReviewTypeSeeder::class,
            PerformanceReviewQuestionSeeder::class,

            CurrencySeeder::class,

            ExecutionSeeder::class,
            ConditionSeeder::class,

            AssetStatusSeeder::class,

            TransactionTypeSeeder::class,

            PurchasePivotSeeder::class,

            ClearanceDepartmentSeeder::class,
        ]);
    }
}
