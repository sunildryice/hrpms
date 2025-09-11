<?php

namespace Modules\EventCompletion\Repositories;

use App\Repositories\Repository;
use Modules\EventCompletion\Models\EventCompletion;

use DB;

class EventCompletionRepository extends Repository
{
    private $fiscalYears;
    
    public function __construct(
        EventCompletion        $eventCompletion
    )
    {
        $this->model = $eventCompletion;
    }

    public function getApproved()
    {
        $authUser = auth()->user();
        $currentOffice = $authUser->getCurrentOffice();
        $accessibleOfficeIds = $authUser->getAccessibleOfficesIds();

        if ($currentOffice) {
            if ($currentOffice->office_type_id == config('constant.HEAD_OFFICE')) {
                return $this->model->with(['requester', 'status'])
                ->select(['*'])
                ->whereIn('status_id', [config('constant.APPROVED_STATUS'), config('constant.AMENDED_STATUS')])
                ->whereIn('office_id', $accessibleOfficeIds)
                ->orWhere(function ($q) {
                    $q->whereNull('office_id');
                    $q->whereIn('status_id', [config('constant.APPROVED_STATUS'), config('constant.AMENDED_STATUS')]);
                })
                ->orderBy('created_at', 'desc')
                ->get();
            }
        }

        return $this->model->with(['requester','status'])
                ->select(['*'])
                ->whereIn('status_id', [config('constant.APPROVED_STATUS'), config('constant.AMENDED_STATUS')])
                ->whereIn('office_id', $accessibleOfficeIds)
                ->orderBy('created_at', 'desc')
                ->get();

    
    }

    public function getEventCompletionsForApproval($authUser)
    {
        return $this->model->whereIn('status_id', [config('constant.SUBMITTED_STATUS'), config('constant.RECOMMENDED_STATUS')])
            ->where('approver_id', '=', $authUser->id)
            ->orderBy('request_date', 'desc')
            ->take(5)
            ->get();
    }

    public function approve($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $eventCompletion = $this->model->find($id);
            if ($inputs['status_id'] == config('constant.RECOMMENDED_STATUS')) {
                $inputs['approver_id'] = $inputs['recommended_to'];
                $inputs['reviewer_id'] = $eventCompletion->approver_id;
            }
            $eventCompletion->update($inputs);
            $eventCompletion->logs()->create($inputs);
            DB::commit();
            return $eventCompletion;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }

    public function cancel($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $eventCompletion = $this->model->find($id);
            $inputs['status_id'] = config('constant.CANCELLED_STATUS');
            $eventCompletion->update($inputs);
            $eventCompletion->logs()->create($inputs);
            DB::commit();
            return $eventCompletion;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }

    public function create($inputs)
    {
        DB::beginTransaction();
        try {
            $inputs['status_id'] = config('constant.CREATED_STATUS');
            $eventCompletion = $this->model->create($inputs);
            DB::commit();
            return $eventCompletion;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $eventCompletion = $this->model->findOrFail($id);
            $eventCompletion->participants()->delete();
            $eventCompletion->logs()->delete();
            $eventCompletion->delete();
            DB::commit();
            return true;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }

    public function forward($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $eventCompletion = $this->model->findOrFail($id);
            $inputs['status_id'] = config('constant.SUBMITTED_STATUS');
            $eventCompletion->update($inputs);
            $eventCompletion->logs()->create($inputs);
            DB::commit();
            return $eventCompletion;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }

    public function update($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $eventCompletion = $this->model->find($id);
            $eventCompletion->fill($inputs)->save();
            
            if ($inputs['btn'] == 'submit') {
                $forwardInputs = [
                    'user_id' => $inputs['updated_by'],
                    'log_remarks' => 'ECR is submitted.',
                    'original_user_id' => $inputs['original_user_id'],
                ];
                $eventCompletion = $this->forward($eventCompletion->id, $forwardInputs);
            }
            DB::commit();
            return $eventCompletion;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }

    public function getECRForApproval($authUser)
    {
        return $this->model->whereIn('status_id', [config('constant.SUBMITTED_STATUS'), config('constant.RECOMMENDED_STATUS')])
            ->where('approver_id', '=', $authUser->id)
            ->orderBy('updated_at', 'desc')
            ->take(5)
            ->get();
    }
}
