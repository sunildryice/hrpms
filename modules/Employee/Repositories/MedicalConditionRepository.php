<?php
namespace Modules\Employee\Repositories;

use App\Repositories\Repository;
use Modules\Employee\Models\MedicalCondition;

class MedicalConditionRepository extends Repository
{
    public function __construct(MedicalCondition $medicalCondition)
    {
        $this->model = $medicalCondition;
    }
}
