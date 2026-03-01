<?php

namespace Modules\Project\Repositories;

use App\Repositories\Repository;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use Modules\Project\Models\Enums\ActivityStatus;
use Modules\Project\Models\ProjectActivity;

class ProjectActivityRepository extends Repository
{
    public function __construct(protected ProjectActivity $projectActivity)
    {
        $this->model = $projectActivity;
    }

    public function getActivitiesByProject($authUser)
    {
        return $this->model
            ->with('members')
            ->whereIn('activity_level', ['activity', 'sub_activity'])
            ->whereHas('members', function ($q) use ($authUser) {
                $q->where('user_id', $authUser->id);
            })
            ->whereHas('project', function ($q) {
                $q->whereNotNull('activated_at');
            })
            ->orderBy('parent_id')
            ->orderBy('title')
            ->get();
    }

    public function getActivitiesByProjectId($authUser, $projectId)
    {
        return $this->model->with('members')
            ->whereIn('activity_level', ['activity', 'sub_activity'])
            ->whereHas('members', function ($q) use ($authUser) {
                $q->where('user_id', $authUser->id);
            })->where('project_id', $projectId)
            ->orderBy('parent_id')
            ->orderBy('title')
            ->get();
    }

    public function create($inputs)
    {
        DB::beginTransaction();
        try {
            $inputs['status'] = ActivityStatus::NotStarted->value;
            $record = $this->model->create($inputs);
            if (isset($inputs['members']) && is_array($inputs['members'])) {
                $record->members()->sync($inputs['members']);
            }
            DB::commit();
            return $record;
        } catch (QueryException $e) {
            logger()->error($e->getMessage());
            DB::rollback();
            throw $e;
        }
    }

    public function update($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $record = $this->model->findOrFail($id);
            $record->update($inputs);
            if (isset($inputs['members']) && is_array($inputs['members'])) {
                $record->members()->sync($inputs['members']);
            }
            DB::commit();
            return $record;
        } catch (QueryException $e) {
            logger()->error($e->getMessage());
            DB::rollback();
            throw $e;
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $record = $this->model->findOrFail($id);
            $record->members()->sync([]);
            $record->timesheets()->delete();
            $record->delete();

            DB::commit();
            return true;
        } catch (QueryException $e) {
            logger()->error($e->getMessage());
            DB::rollback();
            throw $e;
        }
    }
}
