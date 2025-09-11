<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Modules\Master\Models\Execution;

class ExecutionSeeder extends Seeder
{
    /**
     * Run the database seeder
     * @return void
     */
    public function run()
    {
        $execution = new Execution();

        $execution->create([
            'title'         => 'CO Purchased',
            'description'   => 'Country Office Kathmandu' 
        ]);
        $execution->create([
            'title'         => 'ERO Purchased',
            'description'   => 'Eastern Regional Office' 
        ]);
        $execution->create([
            'title'         => 'District Purchased',
            'description'   => 'District Office' 
        ]);
        $execution->create([
            'title'         => 'US Purchased',
            'description'   => 'US Headquarter Office' 
        ]);
        $execution->create([
            'title'         => 'BCO Purchased',
            'description'   => 'Bardibas Cluster Office' 
        ]);
        $execution->create([
            'title'         => 'SCO Purchased',
            'description'   => 'Surkhet Cluster Office' 
        ]);

    }
}