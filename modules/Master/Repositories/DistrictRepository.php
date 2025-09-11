<?php
namespace Modules\Master\Repositories;

use App\Repositories\Repository;
use Modules\Master\Models\District;

class DistrictRepository extends Repository
{
    public function __construct(District $district)
    {
        $this->model = $district;
    }

    public function getDistricts()
    {
        return $this->model->select(['id','district_name'])
            ->orderBy('district_name', 'asc')->get();
    }

    public function getEnabledDistricts()
    {
        return $this->model->select(['id','district_name'])
            ->where('enable_field', 1)
            ->orderBy('district_name', 'asc')->get();
    }
}
