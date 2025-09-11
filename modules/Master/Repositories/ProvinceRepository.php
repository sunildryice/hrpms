<?php
namespace Modules\Master\Repositories;

use App\Repositories\Repository;
use Modules\Master\Models\Province;

class ProvinceRepository extends Repository
{
    public function __construct(Province $province)
    {
        $this->model = $province;
    }
}
