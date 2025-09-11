<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Modules\Master\Models\AssetStatus;

class AssetStatusSeeder extends Seeder
{
    public function run()
    {
        $assetStatus = new AssetStatus();

        $assetStatus->updateOrCreate([
            'id'    => 1,
            'title' => 'new',
            'status_class' => 'created badge bg-primary',
        ]);
        $assetStatus->updateOrCreate([
            'id'    => 2,
            'title' => 'assigned',
            'status_class' => 'created badge bg-secondary',
        ]);
        $assetStatus->updateOrCreate([
            'id'    => 3,
            'title' => 'on maintenance',
            'status_class' => 'created badge bg-warning',
        ]);
        $assetStatus->updateOrCreate([
            'id'    => 4,
            'title' => 'on store',
            'status_class' => 'created badge bg-success',
        ]);
        $assetStatus->updateOrCreate([
            'id'    => 5,
            'title' => 'distributed',
            'status_class' => 'created badge bg-info',
        ]);
        $assetStatus->updateOrCreate([
            'id'    => 6,
            'title' => 'disposed',
            'status_class' => 'created badge bg-danger',
        ]);
    }
}