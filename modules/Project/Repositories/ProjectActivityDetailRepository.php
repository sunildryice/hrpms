<?php

namespace Modules\Project\Repositories;

use Modules\Project\Models\ProjectActivityDetail;

class ProjectActivityDetailRepository
{
    public function create(array $data)
    {
        return ProjectActivityDetail::create($data);
    }

    public function update(ProjectActivityDetail $detail, array $data)
    {
        $detail->update($data);
        return $detail;
    }

    public function find($id)
    {
        return ProjectActivityDetail::find($id);
    }

    public function findByProjectActivity($projectActivityId, $id)
    {
        return ProjectActivityDetail::where('project_activity_id', $projectActivityId)->where('id', $id)->first();
    }

    public function delete(ProjectActivityDetail $detail)
    {
        return $detail->delete();
    }
}
