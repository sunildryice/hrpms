<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RiskLookupSeeder extends Seeder
{
    public function run(): void
    {
        // lkup_risk_status
        $statuses = [
            'New/Emerging',
            'Ongoing',
            'Closed',
        ];

        foreach ($statuses as $title) {
            DB::table('lkup_risk_status')->updateOrInsert(
                ['title' => $title],
                ['created_at' => now(), 'updated_at' => now()]
            );
        }

        // lkup_risk_types
        $types = [
            'Contextual risk',
            'Delivery risk',
            'Safeguarding risk',
            'Operational risk',
            'Fiduciary risk',
            'Reputational risk',
        ];

        foreach ($types as $title) {
            DB::table('lkup_risk_types')->updateOrInsert(
                ['title' => $title],
                ['created_at' => now(), 'updated_at' => now()]
            );
        }

        // lkup_risk_probabilitis
        $probabilities = [
            'Rare',
            'Unlikely',
            'Possible',
            'Likely',
            'Almost certain',
        ];

        foreach ($probabilities as $title) {
            DB::table('lkup_risk_probabilitis')->updateOrInsert(
                ['title' => $title],
                ['created_at' => now(), 'updated_at' => now()]
            );
        }

        // lkup_risk_impacts
        $impacts = [
            'Insignificant',
            'Minor',
            'Moderate',
            'Major',
            'Severe',
        ];

        foreach ($impacts as $title) {
            DB::table('lkup_risk_impacts')->updateOrInsert(
                ['title' => $title],
                ['created_at' => now(), 'updated_at' => now()]
            );
        }

        // lkup_risk_ratings
        $ratings = [
            'Minor',
            'Moderate',
            'Major',
            'Severe',
        ];

        foreach ($ratings as $title) {
            DB::table('lkup_risk_ratings')->updateOrInsert(
                ['title' => $title],
                ['created_at' => now(), 'updated_at' => now()]
            );
        }

        // lkup_risk_response_types
        $responseTypes = [
            'Avoid',
            'Reduce',
            'Transfer',
            'Share',
            'Accept',
            'Prepare contingent plan',
        ];

        foreach ($responseTypes as $title) {
            DB::table('lkup_risk_response_types')->updateOrInsert(
                ['title' => $title],
                ['created_at' => now(), 'updated_at' => now()]
            );
        }
    }
}
