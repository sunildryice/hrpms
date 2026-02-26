<?php

namespace Modules\Master\Repositories;

use App\Repositories\Repository;
use Modules\Master\Models\Brand;

class BrandRepository extends Repository
{
    public function __construct(Brand $brand)
    {
        $this->model = $brand;
    }
}