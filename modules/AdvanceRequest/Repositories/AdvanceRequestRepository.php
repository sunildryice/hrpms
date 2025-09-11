<?php

namespace Modules\AdvanceRequest\Repositories;


use App\Repositories\Repository;
use Modules\AdvanceRequest\Models\AdvanceRequest;

use DB;
use Modules\Master\Repositories\FiscalYearRepository;

class AdvanceRequestRepository extends Repository
{
    private $fiscalYears;
    public function __construct(
        AdvanceRequest $advanceRequest,
        FiscalYearRepository $fiscalYears
    ){
        $this->model = $advanceRequest;
        $this->fiscalYears = $fiscalYears;
    }

    public function getApproved()
    {
        $authUser = auth()->user();
        $currentOffice = $authUser->getCurrentOffice();
        $accessibleOfficeIds = $authUser->getAccessibleOfficesIds();

        if ($currentOffice) {
            if ($currentOffice->office_type_id == config('constant.HEAD_OFFICE')) {
                return $this->model
                ->with(['fiscalYear', 'status', 'advanceRequestDetails','projectCodes','office', 'requester', 'advanceSettlement', 'district'])
                ->select(['*'])
                ->whereStatusId(config('constant.APPROVED_STATUS'))
                ->where(function ($q) use ($authUser, $accessibleOfficeIds) {
                    $q->where('verifier_id', $authUser->id);
                    $q->orwhere('reviewer_id', $authUser->id);
                    $q->orwhere('approver_id', $authUser->id);
                    $q->orwhere('created_by', $authUser->id);
                    $q->orWhereIn('office_id', $accessibleOfficeIds);
                })
                ->orWhere(function ($q) {
                    $q->whereNull('office_id');
                    $q->whereIn('status_id', [config('constant.APPROVED_STATUS')]);
                })
                ->orderBy('created_at', 'desc')
                ->get();
            }
        }

        return $this->model
        ->with(['fiscalYear', 'status', 'advanceRequestDetails','projectCodes','office', 'requester', 'advanceSettlement', 'district'])
        ->select(['*'])
        ->whereStatusId(config('constant.APPROVED_STATUS'))
        ->where(function ($q) use ($authUser, $accessibleOfficeIds) {
            $q->where('verifier_id', $authUser->id);
            $q->orwhere('reviewer_id', $authUser->id);
            $q->orwhere('approver_id', $authUser->id);
            $q->orwhere('created_by', $authUser->id);
            $q->orWhereIn('office_id', $accessibleOfficeIds);
        })
        ->orderBy('created_at', 'desc')
        ->get();
    }

    public function getPaid()
    {
        $authUser = auth()->user();
        $currentOffice = $authUser->getCurrentOffice();
        $accessibleOfficeIds = $authUser->getAccessibleOfficesIds();

        if ($currentOffice) {
            if ($currentOffice->office_type_id == config('constant.HEAD_OFFICE')) {
                return $this->model
                ->with(['fiscalYear', 'status', 'advanceRequestDetails'])
                ->select(['*'])
                ->whereStatusId(config('constant.PAID_STATUS'))
                ->where(function ($q) use ($authUser, $accessibleOfficeIds) {
                    $q->where('verifier_id', $authUser->id);
                    $q->orwhere('reviewer_id', $authUser->id);
                    $q->orwhere('approver_id', $authUser->id);
                    $q->orwhere('created_by', $authUser->id);
                    $q->orWhereIn('office_id', $accessibleOfficeIds);
                })
                ->orWhere(function ($q) {
                    $q->whereNull('office_id');
                    $q->whereIn('status_id', [config('constant.PAID_STATUS')]);
                })
                ->orderBy('created_at', 'desc')
                ->get();
            }
        }

        return $this->model
        ->with(['fiscalYear', 'status', 'advanceRequestDetails'])
        ->select(['*'])
        ->whereStatusId(config('constant.PAID_STATUS'))
        ->where(function ($q) use ($authUser, $accessibleOfficeIds) {
            $q->where('verifier_id', $authUser->id);
            $q->orwhere('reviewer_id', $authUser->id);
            $q->orwhere('approver_id', $authUser->id);
            $q->orwhere('created_by', $authUser->id);
            $q->orWhereIn('office_id', $accessibleOfficeIds);
        })
        ->orderBy('created_at', 'desc')
        ->get();
    }

public function getClosed()
    {
        $authUser = auth()->user();
        $currentOffice = $authUser->getCurrentOffice();
        $accessibleOfficeIds = $authUser->getAccessibleOfficesIds();

        if ($currentOffice) {
            if ($currentOffice->office_type_id == config('constant.HEAD_OFFICE')) {
                return $this->model
                ->with(['fiscalYear', 'status', 'advanceRequestDetails'])
                ->select(['*'])
                ->whereStatusId(config('constant.CLOSED_STATUS'))
                ->where(function ($q) use ($authUser, $accessibleOfficeIds) {
                    $q->where('verifier_id', $authUser->id);
                    $q->orwhere('reviewer_id', $authUser->id);
                    $q->orwhere('approver_id', $authUser->id);
                    $q->orwhere('created_by', $authUser->id);
                    $q->orWhereIn('office_id', $accessibleOfficeIds);
                })
                ->orWhere(function ($q) {
                    $q->whereNull('office_id');
                    $q->whereIn('status_id', [config('constant.APPROVED_STATUS')]);
                })
                ->orderBy('created_at', 'desc')
                ->get();
            }
        }

        return $this->model
        ->with(['fiscalYear', 'status', 'advanceRequestDetails'])
        ->select(['*'])
        ->whereStatusId(config('constant.CLOSED_STATUS'))
        ->where(function ($q) use ($authUser, $accessibleOfficeIds) {
            $q->where('verifier_id', $authUser->id);
            $q->orwhere('reviewer_id', $authUser->id);
            $q->orwhere('approver_id', $authUser->id);
            $q->orwhere('created_by', $authUser->id);
            $q->orWhereIn('office_id', $accessibleOfficeIds);
        })
        ->orderBy('created_at', 'desc')
        ->get();
    }

    public function generateAdvanceNumber($fiscalYearId)
    {
        $max = $this->model->select(['fiscal_year_id', 'advance_number'])
                ->where('fiscal_year_id', $fiscalYearId)
                ->max('advance_number') + 1;
        return $max;
    }


      public function create($inputs)
    {
        DB::beginTransaction();
        try {
            $inputs['status_id'] = 1;
            $advanceRequest = $this->model->create($inputs);
            DB::commit();
            return $advanceRequest;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }

       public function forward($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $advanceRequest = $this->model->findOrFail($id);
            $inputs['status_id'] = config('constant.SUBMITTED_STATUS');
            if (!$advanceRequest->advance_number) {
                $fiscalYear = $this->fiscalYears->where('start_date', '<=', date('Y-m-d'))
                    ->where('end_date', '>=', date('Y-m-d'))
                    ->first();

                $inputs['prefix'] = 'ADV';
                $inputs['request_date'] = date('Y-m-d');
                $inputs['fiscal_year_id'] = $fiscalYear->id;
                $inputs['advance_number'] = $this->generateAdvanceNumber($fiscalYear->id);
            }
            $advanceRequest->update($inputs);
            $advanceRequest->logs()->create($inputs);
            DB::commit();
            return $advanceRequest;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }

    public function update($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $advanceRequest = $this->model->find($id);
            $advanceRequest->fill($inputs)->save();
            if ($inputs['btn'] == 'submit') {
                $forwardInputs = [
                    'user_id' => $inputs['updated_by'],
                    'log_remarks' => 'Advance request is submitted.',
                    'original_user_id' => $inputs['original_user_id'],
                ];
                $advanceRequest = $this->forward($advanceRequest->id, $forwardInputs);
            }
            DB::commit();
            return $advanceRequest;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }


    public function approve($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $advanceRequest = $this->model->find($id);
            if($inputs['status_id'] == config('constant.RECOMMENDED_STATUS')){
                $inputs['approver_id'] = $inputs['recommended_to'];
                $inputs['reviewer_id'] = $advanceRequest->approver_id;
            }
            $advanceRequest->update($inputs);
            $advanceRequest->logs()->create($inputs);
            DB::commit();
            return $advanceRequest;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }

    public function destroy($id)
    {
        try {
            $advanceRequest = $this->model->findOrFail($id);
            $advanceRequest->logs()->delete();
            $advanceRequest->advanceRequestDetails()->delete();
            $advanceRequest->delete();
            return true;
        } catch (\Illuminate\Database\QueryException $e) {
            return false;
        }
    }

    public function verify($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $advanceRequest = $this->model->find($id);
            $advanceRequest->update($inputs);
            $advanceRequest->logs()->create($inputs);
            DB::commit();
            return $advanceRequest;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }

    public function close($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $advanceRequest = $this->model->find($id);
            $inputs['status_id'] = config('constant.CLOSED_STATUS');
            $inputs['updated_by'] = auth()->user()->id;
            $inputs['closed_at'] = date('Y-m-d H:i:s');
            $advanceRequest->update($inputs);
            $inputs['user_id'] = $inputs['updated_by'];
            $inputs['log_remarks'] = 'Advance request closed.';
            $advanceRequest->logs()->create($inputs);
            DB::commit();
            return $advanceRequest;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            return false;
        }
    }

    public function pay($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $advance = $this->model->find($id);
            $inputs['log_remarks'] = $inputs['payment_remarks'];
            $inputs['status_id'] = config('constant.PAID_STATUS');
            $advance->update($inputs);
            $advance->logs()->create($inputs);
            DB::commit();
            return $advance;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            return false;
        }
    }

}
