<?php 

// repo for project activity timesheet
namespace Modules\Project\Repositories;

use Modules\Project\Models\ActivityTimeSheet;
use App\Repositories\Repository;
class ActivityTimeSheetRepository extends Repository
{
    public function __construct(ActivityTimeSheet $model)
    {
        $this->model = $model;
    }

}