<?php

namespace Modules\Master\Repositories;

use App\Repositories\Repository;
use Modules\Master\Models\Country;

class CountryRepository extends Repository
{
    public function __construct(Country $country)
    {
        $this->model = $country;
    }

    public function getActiveCountries()
    {
        return $this->model->select(['*'])
            ->where('enable_field', true)
            ->orderBy('title')->get();
    }
}
