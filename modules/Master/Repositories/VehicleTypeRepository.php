<?php
namespace Modules\Master\Repositories;

use App\Repositories\Repository;
use Modules\Master\Models\VehicleType;

class VehicleTypeRepository extends Repository
{
    public function __construct(VehicleType $vehicleType)
    {
        $this->model = $vehicleType;
    }

    public function getSelectiveVehicleTypes()
    {
        return $this->model
            ->whereIn('title', [
                'Car',
                'Pick up Jeep',
            ])
            // ->orWhereIn('id', [3, 6]) 
            ->orderBy('title')
            ->get();
    }
}
