<?php

namespace Modules\EmployeeExit\Repositories;

use App\Repositories\Repository;
use DB;
use Modules\EmployeeExit\Models\ExitHandOverNote;
use Modules\Privilege\Repositories\UserRepository;

class ExitHandOverNoteRepository extends Repository
{
    public function __construct(
        ExitHandOverNote $exitHandOverNote
    ) {
        $this->model = $exitHandOverNote;
    }

    public function approve($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $exitHandOverNote = $this->model->find($id);
            $exitHandOverNote->update($inputs);
            $exitHandOverNote->logs()->create($inputs);
            DB::commit();

            return $exitHandOverNote;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();

            return false;
        }
    }

    public function create($inputs)
    {
        DB::beginTransaction();
        try {
            $inputs['status_id'] = 1;
            $exitHandOverNote = $this->model->create($inputs);
            $exitHandOverNote->employeeExitPayable()->create($inputs);
            $exitHandOverNote->exitInterview()->create($inputs);
            $exitHandOverNote->exitAssetHandover()->create($inputs);
            $staffClearance = $exitHandOverNote->staffClearance()->create($inputs);
            $staffClearance->logs()->create([
                'user_id' => auth()->id(),
                'status_id' => config('constant.CREATED_STATUS'),
                'log_remarks' => 'Staff Clearance is created.',
            ]);
            DB::commit();

            return $exitHandOverNote;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            dd($e);

            return false;
        }
    }

    public function forward($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $exitHandOverNote = $this->model->findOrFail($id);
            $inputs['status_id'] = config('constant.SUBMITTED_STATUS');
            $exitHandOverNote->update($inputs);
            $exitHandOverNote->logs()->create($inputs);
            DB::commit();

            return $exitHandOverNote;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();

            return false;
        }
    }

    public function update($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $exitHandOverNote = $this->model->find($id);
            $exitHandOverNote->fill($inputs)->save();

            if ($inputs['btn'] == 'submit') {
                $forwardInputs = [
                    'request_date' => date('Y-m-d'),
                    'user_id' => $inputs['updated_by'],
                    'log_remarks' => 'Exit Hand Over Note is submitted.',
                    'original_user_id' => $inputs['original_user_id'],
                ];
                $exitHandOverNote = $this->forward($exitHandOverNote->id, $forwardInputs);
            }
            DB::commit();

            return $exitHandOverNote;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();

            return false;
        }
    }

    public function destroy($id)
    {
        try {
            $exitHandOverNote = $this->model->findOrFail($id);
            $exitHandOverNote->employeeExitPayable()->delete();
            $exitHandOverNote->exitInterview()->delete();
            $exitHandOverNote->exitAssetHandover()->delete();
            $exitHandOverNote->staffClearance()->delete();
            $exitHandOverNote->delete();
            return true;
        } catch (\Illuminate\Database\QueryException $e) {
            return false;
        }
    }
}
