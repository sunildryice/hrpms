<?php
namespace Modules\Master\Repositories;

use App\Repositories\Repository;
use Modules\Master\Models\TravelMode;

class TravelModeRepository extends Repository
{
    public function __construct(TravelMode $travelModes)
    {
        $this->model = $travelModes;
    }
}
