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

    public function getEnableVehicleTypes()
    {
        return $this->model
            ->where('enable_office', true)
            ->orderBy('title')
            ->get();
    }
}
