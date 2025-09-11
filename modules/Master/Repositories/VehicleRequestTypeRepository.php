<?php
namespace Modules\Master\Repositories;

use App\Repositories\Repository;
use Modules\Master\Models\VehicleRequestType;

class VehicleRequestTypeRepository extends Repository
{
    public function __construct(VehicleRequestType $vehicleRequestType)
    {
        $this->model = $vehicleRequestType;
    }
}
