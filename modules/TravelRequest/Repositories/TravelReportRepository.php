<?php

namespace Modules\TravelRequest\Repositories;

use App\Repositories\Repository;
use Modules\TravelRequest\Models\TravelReport;

use DB;

class TravelReportRepository extends Repository
{
    public function __construct(TravelReport $travelReport)
    {
        $this->model = $travelReport;
    }

    public function getApproved()
    {
        $authUser = auth()->user();
        $currentOffice = $authUser->getCurrentOffice();
        $accessibleOfficeIds = $authUser->getAccessibleOfficesIds();

        if ($currentOffice) {
            if ($currentOffice->office_type_id == config('constant.HEAD_OFFICE')) {
                return $this->model->with(['travelRequest'])->select(['*'])
                ->whereIn('status_id', [config('constant.APPROVED_STATUS'), config('constant.AMENDED_STATUS')])
                ->orWhere(function ($q) use ($authUser) {
                    $q->where('approver_id', $authUser->id);
                    $q->orwhere('created_by', $authUser->id);
                })
                ->whereHas('travelRequest', function ($q) use ($accessibleOfficeIds) {
                    $q->whereIn('office_id', $accessibleOfficeIds);
                    $q->orWhere(function ($q) {
                        $q->whereNull('office_id');
                        $q->whereIn('status_id', [config('constant.APPROVED_STATUS'), config('constant.AMENDED_STATUS')]);
                    });
                })
                ->orderBy('created_at', 'desc')
                ->get();
            }
        }

        return $this->model->with(['travelRequest'])->select(['*'])
        ->whereIn('status_id', [config('constant.APPROVED_STATUS'), config('constant.AMENDED_STATUS')])
        ->where(function ($q) use ($authUser) {
            $q->where('approver_id', $authUser->id);
            $q->orwhere('created_by', $authUser->id);
        })
        ->orWhereHas('travelRequest', function ($q) use ($accessibleOfficeIds) {
            $q->whereIn('office_id', $accessibleOfficeIds);
        })
        ->orderBy('created_at', 'desc')
        ->get();
    }

    public function approve($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $travelReport = $this->model->find($id);
            $travelReport->update($inputs);
            $travelReport->logs()->create($inputs);
            DB::commit();
            return $travelReport;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }

    }

    public function create($inputs)
    {
        DB::beginTransaction();
        try {
            $travelReport = $this->model->updateOrCreate(['travel_request_id' => $inputs['travel_request_id']], $inputs);
            if (!empty($inputs['recommendation_input'])) {
                foreach ($inputs['recommendation_input'] as $recommendation) {
                    $travelReport->travelReportRecommendations()->create($recommendation);
                }
            }
            if ($inputs['btn'] == 'submit') {
                $forwardInputs = [
                    'user_id' => $inputs['updated_by'],
                    'log_remarks' => 'Travel report is submitted.',
                    'original_user_id' => $inputs['original_user_id'],
                    'approver_id' => $inputs['approver_id'],
                    'status_id' => config('constant.SUBMITTED_STATUS'),
                ];
                $this->forward($travelReport->id, $forwardInputs);
            }

            DB::commit();
            return $travelReport;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $travelReport = $this->model->findOrFail($id);
            $travelReport->travelReportRecommendations()->delete();
            $travelReport->logs()->delete();
            $travelReport->delete();
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
            $travelReport = $this->model->findOrFail($id);
            $travelReport->update($inputs);
            $travelReport->logs()->create($inputs);
            DB::commit();
            return $travelReport;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }

    public function update($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $travelReport = $this->model->findOrFail($id);
            $travelReport->fill($inputs)->save();
            $travelReport->travelReportRecommendations()->delete();
            if (!empty($inputs['recommendation_input'])) {
                foreach ($inputs['recommendation_input'] as $recommendation) {
                    $travelReport->travelReportRecommendations()->create($recommendation);
                }
            }
            if ($inputs['btn'] == 'submit') {
                $forwardInputs = [
                    'user_id' => $inputs['updated_by'],
                    'log_remarks' => 'Travel report is submitted.',
                    'original_user_id' => $inputs['original_user_id'],
                    'approver_id' => $inputs['approver_id'],
                    'status_id' => config('constant.SUBMITTED_STATUS'),
                ];
                $travelReport = $this->forward($travelReport->id, $forwardInputs);
            }

            DB::commit();
            return $travelReport;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }

}
