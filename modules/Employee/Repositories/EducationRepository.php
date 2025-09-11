<?php
namespace Modules\Employee\Repositories;

use App\Repositories\Repository;
use Modules\Employee\Models\Education;

class EducationRepository extends Repository
{
    public function __construct(Education $education)
    {
        $this->model = $education;
    }
}
