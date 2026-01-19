<?php

namespace Modules\Project\Repositories;

use App\Repositories\Repository;
use Modules\Project\Models\Project\ProjectActivity;

class ProjectActivityRepository extends Repository
{
    public function __construct(protected ProjectActivity $projectActivity)
    {
        $this->model = $projectActivity;
    }
}
