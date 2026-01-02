<?php

namespace Modules\Project\Repositories;

use App\Repositories\Repository;
use Modules\Project\Models\Project;

class ProjectRepository extends Repository
{
    public function __construct(Project $model)
    {
        $this->model = $model;
    }
}
