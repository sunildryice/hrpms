<?php

namespace Modules\MaintenanceRequest\Repositories;

use App\Repositories\Repository;
use DB;
use Modules\MaintenanceRequest\Models\MaintenanceRequest;
use Modules\Master\Repositories\FiscalYearRepository;

class MaintenanceRequestRepository extends Repository
{
    private $fiscalYears;

    public function __construct(
        FiscalYearRepository $fiscalYears,
        MaintenanceRequest $maintenanceRequest
    ) {
        $this->fiscalYears = $fiscalYears;
        $this->model = $maintenanceRequest;
    }

    public function getApproved()
    {
        $authUser = auth()->user();
        $currentOffice = $authUser->getCurrentOffice();
        $accessibleOfficeIds = $authUser->getAccessibleOfficesIds();

        if ($currentOffice) {
            if ($currentOffice->office_type_id == config('constant.HEAD_OFFICE')) {
                return $this->model
                    ->select(['*'])
                    ->whereIn('status_id', [config('constant.APPROVED_STATUS')])
                    ->whereIn('office_id', $accessibleOfficeIds)
                    ->orWhere(function ($q) {
                        $q->whereNull('office_id');
                        $q->whereIn('status_id', [config('constant.APPROVED_STATUS')]);
                    })
                    ->orderBy('created_at', 'desc')
                    ->get();
            }
        }

        return $this->model
            ->select(['*'])
            ->whereIn('status_id', [config('constant.APPROVED_STATUS')])
            ->whereIn('office_id', $accessibleOfficeIds)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function generateMaintenanceNumber($fiscalYearId)
    {
        $max = $this->model->select(['fiscal_year_id', 'maintenance_number'])
            ->where('fiscal_year_id', $fiscalYearId)
            ->max('maintenance_number') + 1;

        return $max;
    }

    public function approve($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $maintenanceRequest = $this->model->find($id);
            $maintenanceRequest->update($inputs);
            $maintenanceRequest->logs()->create($inputs);
            DB::commit();

            return $maintenanceRequest;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();

            return false;
        }
    }

    public function forward($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $maintenanceRequest = $this->model->findOrFail($id);
            if (! $maintenanceRequest->maintenance_number) {
                $fiscalYear = $this->fiscalYears->where('start_date', '<=', date('Y-m-d'))
                    ->where('end_date', '>=', date('Y-m-d'))
                    ->first();

                $inputs['fiscal_year_id'] = $fiscalYear->id;
                $inputs['prefix'] = 'MR';
                $inputs['maintenance_number'] = $this->generateMaintenanceNumber($fiscalYear->id);
            }
            $maintenanceRequest->update($inputs);
            $inputs['status_id'] = 3;
            $maintenanceRequest->logs()->create($inputs);
            DB::commit();

            return $maintenanceRequest;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();

            return false;
        }
    }

    public function review($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $maintenanceRequest = $this->model->find($id);
            $maintenanceRequest->update($inputs);
            $maintenanceRequest->logs()->create($inputs);
            DB::commit();

            return $maintenanceRequest;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();

            return false;
        }
    }

    public function update($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $maintenanceRequest = $this->model->find($id);
            $maintenanceRequest->fill($inputs)->save();
            if ($inputs['btn'] == 'submit') {
                $forwardInputs = [
                    'status_id' => $inputs['reviewer_id'] ? 3 : 11,
                    'log_remarks' => 'Maintenance request is submitted.',
                    'user_id' => $maintenanceRequest->created_by,
                    'original_user_id' => $inputs['original_user_id'],
                ];
                $maintenanceRequest = $this->forward($maintenanceRequest->id, $forwardInputs);
            }
            DB::commit();

            return $maintenanceRequest;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();

            return false;
        }
    }

    public function updateTotalAmount($maintenanceRequestId)
    {
        DB::beginTransaction();
        try {
            $maintenanceRequest = $this->model->findOrFail($maintenanceRequestId);
            $estimatedCost = $maintenanceRequest->maintenanceRequestItems->sum('estimated_cost');
            $updateInputs = [
                'estimated_cost' => $estimatedCost,
            ];
            $maintenanceRequest->update($updateInputs);
            DB::commit();

            return $maintenanceRequest;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();

            return false;
        }
    }

    public function amend($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $maintenance = $this->model->find($id);
            $maintenance->update(['status_id' => config('constant.AMENDED_STATUS')]);
            $clone = $maintenance->replicate();
            unset($clone->reviewer_id);
            unset($clone->verifier_id);
            unset($clone->recommender_id);
            unset($clone->approver_id);
            $clone->status_id = config('constant.CREATED_STATUS');
            $clone->request_date = date('Y-m-d');
            $clone->created_by = $inputs['created_by'];
            $clone->modification_maintenance_request_id = $maintenance->id;
            $parentMaintenanceId = $maintenance->modification_maintenance_request_id ?: $maintenance->id;
            $clone->modification_number = $this->model->where('modification_maintenance_request_id', $parentMaintenanceId)
                ->max('modification_number') + 1;
            $clone->modification_remarks = $inputs['modification_remarks'];
            $clone->save();

            if ($maintenance->districts) {
                $districtIds = $maintenance->districts->map(function ($district) {
                    return $district->id;
                })->toArray();
                $clone->districts()->sync($districtIds);
            }

            foreach ($maintenance->maintenanceRequestItems as $maintenanceItem) {
                unset($maintenanceItem->id);
                unset($maintenanceItem->maintenance_id);
                $maintenanceItemInputs = $maintenanceItem->toArray();
                $maintenanceItemInputs['created_by'] = $inputs['created_by'];
                $clone->maintenanceRequestItems()->create($maintenanceItemInputs);
            }
            DB::commit();

            return $clone;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();

            return false;
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $maintenanceRequest = $this->model->findOrFail($id);

            if ($maintenanceRequest->parentMaintenanceRequest) {
                $parentMaintenanceRequest = $maintenanceRequest->parentMaintenanceRequest;
                $parentMaintenanceRequest->update(['status_id' => config('constant.APPROVED_STATUS')]);
            }
            $maintenanceRequest->logs()->delete();
            $maintenanceRequest->maintenanceRequestItems()->delete();
            $maintenanceRequest->delete();
            DB::commit();
            return true;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }
}
