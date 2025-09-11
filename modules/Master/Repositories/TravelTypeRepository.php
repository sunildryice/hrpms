<?php
namespace Modules\Master\Repositories;

use App\Repositories\Repository;
use Modules\Master\Models\TravelType;

class TravelTypeRepository extends Repository
{
    public function __construct(TravelType $travelType)
    {
        $this->model = $travelType;
    }
}
