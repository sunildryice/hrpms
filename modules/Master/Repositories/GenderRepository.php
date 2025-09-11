<?php
namespace Modules\Master\Repositories;

use App\Repositories\Repository;
use Modules\Master\Models\Gender;

class GenderRepository extends Repository
{
    public function __construct(Gender $gender)
    {
        $this->model = $gender;
    }
}
