<?php
namespace Modules\Employee\Repositories;

use App\Repositories\Repository;
use Modules\Employee\Models\Training;

class TrainingRepository extends Repository
{
    public function __construct(Training $training)
    {
        $this->model = $training;
    }
}
