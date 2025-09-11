<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProvinceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $query = "INSERT INTO `lkup_provinces` (`id`, `province_name`, `created_at`, `updated_at`) VALUES
(1, 'Province 1', '2022-03-23 12:34:31', '2022-03-23 12:34:31'),
(2, 'Madhesh', '2022-03-23 12:34:31', '2022-03-23 12:34:31'),
(3, 'Bagmati', '2022-03-23 12:34:31', '2022-03-23 12:34:31'),
(4, 'Gandaki', '2022-03-23 12:34:31', '2022-03-23 12:34:31'),
(5, 'Lumbini', '2022-03-23 12:34:31', '2022-03-23 12:34:31'),
(6, 'Karnali', '2022-03-23 12:34:31', '2022-03-23 12:34:31'),
(7, 'Sudur Paschim', '2022-03-23 12:34:31', '2022-03-23 12:34:31');
";
        \DB::insert($query);
    }
}
