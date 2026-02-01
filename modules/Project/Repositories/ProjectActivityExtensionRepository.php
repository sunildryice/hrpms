<?php

namespace Modules\Project\Repositories;

use App\Repositories\Repository;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use Modules\Project\Models\ProjectActivity;
use Modules\Project\Models\Enums\ActivityStatus;
use Modules\Project\Models\ProjectActivityExtension;

class ProjectActivityExtensionRepository extends Repository
{
    public function __construct(protected ProjectActivityExtension $projectActivityExtension)
    {
        $this->model = $projectActivityExtension;
    }

    public function create($inputs)
    {
        DB::beginTransaction();
        try {
            $record = $this->model->create($inputs);
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
