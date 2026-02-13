<?php

namespace Modules\Project\Repositories;

use Modules\Project\Models\ActivityOtherDetail;

class ActivityOtherDetailRepository
{
    public function create(array $data)
    {
        return ActivityOtherDetail::create($data);
    }

    public function update(ActivityOtherDetail $detail, array $data)
    {
        $detail->update($data);
        return $detail;
    }

    public function find($id)
    {
        return ActivityOtherDetail::find($id);
    }

    public function findByProjectActivity($projectActivityId, $id)
    {
        return ActivityOtherDetail::where('project_activity_id', $projectActivityId)->where('id', $id)->first();
    }

    public function delete(ActivityOtherDetail $detail)
    {
        return $detail->delete();
    }
}
