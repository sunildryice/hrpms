<?php
namespace Modules\Master\Repositories;

use App\Repositories\Repository;
use Modules\Master\Models\ProjectCode;

class ProjectCodeRepository extends Repository
{
    public function __construct(ProjectCode $projectCode)
    {
        $this->model = $projectCode;
    }

    public function getActiveProjectCodes()
    {
        return $this->model->select(['id', 'title','description'])
            ->whereNotNull('activated_at')
            ->orderBy('title', 'asc')->get();
    }
}
