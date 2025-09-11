<?php
namespace Modules\Master\Repositories;

use App\Repositories\Repository;
use Modules\Master\Models\Vehicle;

class VehicleRepository extends Repository
{
    public function __construct(Vehicle $vehicles)
    {
        $this->model = $vehicles;
    }

    public function getActiveVehicles()
    {
        return $this->model->select(['*'])
            ->whereNotNull('activated_at')
            ->orderBy('vehicle_number')->get();
    }
}
