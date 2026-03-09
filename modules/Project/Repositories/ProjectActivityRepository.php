<?php

namespace Modules\Project\Repositories;

use App\Repositories\Repository;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use Modules\Project\Models\Enums\ActivityStatus;
use Modules\Project\Models\Project;
use Modules\Project\Models\ProjectActivity;

class ProjectActivityRepository extends Repository
{
    public function __construct(
        protected Project         $project,
        protected ProjectActivity $projectActivity
    )
    {
        $this->project = $project;
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
        $project = $this->project->find($projectId);
        if ($project->focal_person_id == $authUser->id || $project->team_lead_id == $authUser->id) {
            return $this->model->with('members')
                ->whereIn('activity_level', ['activity', 'sub_activity'])
                ->where('project_id', $projectId)
                ->orderBy('parent_id')
                ->orderBy('title')
                ->get();
        } else {
            return $this->model->with('members')
                ->whereIn('activity_level', ['activity', 'sub_activity'])
                ->whereHas('members', function ($q) use ($authUser) {
                    $q->where('user_id', $authUser->id);
                })->where('project_id', $projectId)
                ->orderBy('parent_id')
                ->orderBy('title')
                ->get();
        }
    }

    public function getActivitiesDetail()
    {
        $query = $this->model
            ->select([
                'project_activities.*',
                'projects.title as project_title',
                'parent.title as parent_title',
                'lkup_activity_stages.title as stage_title',
            ])
            ->join('projects', 'project_activities.project_id', '=', 'projects.id')
            ->leftJoin('project_activities as parent', 'project_activities.parent_id', '=', 'parent.id')
            ->leftJoin('lkup_activity_stages', 'project_activities.activity_stage_id', '=', 'lkup_activity_stages.id')
            ->whereIn('project_activities.activity_level', ['activity', 'sub_activity'])
            ->whereHas('members')
            ->with('members:id,full_name')
            ->orderBy('projects.title')
            ->orderBy('project_activities.parent_id')
            ->orderBy('project_activities.activity_level')
            ->orderBy('project_activities.title');

        return $query;
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
