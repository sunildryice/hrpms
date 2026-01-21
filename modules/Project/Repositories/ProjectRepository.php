<?php

namespace Modules\Project\Repositories;

use DB;
use App\Repositories\Repository;
use Modules\Project\Models\Project;

class ProjectRepository extends Repository
{
    public function __construct(Project $model)
    {
        $this->model = $model;
    }

    public function create($inputs)
    {
        DB::beginTransaction();
        try {
            $project = $this->model->create($inputs);
            if (!empty($inputs['stages'])) {
                $project->stages()->sync($inputs['stages']);
            }
            if (!empty($inputs['members'])) {
                $project->members()->sync($inputs['members']);
            }
            DB::commit();
            return $project;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }

    public function update($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $project = $this->model->find($id);
            $project->fill($inputs)->save();

            if (!empty($inputs['stages'])) {
                $project->stages()->sync($inputs['stages']);
            } else {
                $project->stages()->sync([]);
            }

            if (!empty($inputs['members'])) {
                $project->members()->sync($inputs['members']);
            } else {
                $project->members()->sync([]);
            }
            DB::commit();

            return $project;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }
}
