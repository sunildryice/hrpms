<?php

namespace Modules\Master\Repositories;

use App\Repositories\Repository;
use Modules\Master\Models\ProjectTheme;

class ProjectThemeRepository extends Repository
{
    public function __construct(ProjectTheme $model)
    {
        $this->model = $model;
    }

    public function getActive()
    {
        return $this->model->where('enable_field', true)->orderBy('title')->get();
    }
}