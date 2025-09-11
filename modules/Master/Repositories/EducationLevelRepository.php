<?php
namespace Modules\Master\Repositories;

use App\Repositories\Repository;
use Modules\Master\Models\EducationLevel;

class EducationLevelRepository extends Repository
{
    public function __construct(EducationLevel $educationLevel)
    {
        $this->model = $educationLevel;
    }
}
