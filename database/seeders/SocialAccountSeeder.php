<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SocialAccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $socialMediaAccounts = ['Facebook', 'Twitter', 'LinkedIn',];

        DB::table('lkup_social_accounts')->insert(
            collect($socialMediaAccounts)->map(function ($account) {
                return [
                    'title' => $account,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            })->toArray()
        );
    }
}
