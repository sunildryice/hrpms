<?php

namespace Modules\VehicleRequest\Repositories;

use App\Repositories\Repository;
use DB;
use Modules\Master\Models\FiscalYear;
use Modules\VehicleRequest\Models\VehicleRequest;

class VehicleRequestRepository extends Repository
{
    public function __construct(
        FiscalYear $fiscalYear,
        VehicleRequest $vehicleRequest
    ) {
        $this->fiscalYear = $fiscalYear;
        $this->model = $vehicleRequest;
    }

    public function getApproved()
    {
        $authUser = auth()->user();
        $currentOffice = $authUser->getCurrentOffice();
        $accessibleOfficeIds = $authUser->getAccessibleOfficesIds();

        if ($currentOffice) {
            if ($currentOffice->office_type_id == config('constant.HEAD_OFFICE')) {

                return $this->model->with(['status', 'vehicleRequestType'])->select(['*'])
                    ->whereIn('status_id', [config('constant.APPROVED_STATUS'), config('constant.ASSIGNED_STATUS')])
                    ->whereIn('office_id', $accessibleOfficeIds)
                    ->orWhere(function ($q) {
                        $q->whereNull('office_id');
                        $q->whereIn('status_id', [config('constant.APPROVED_STATUS'), config('constant.ASSIGNED_STATUS')]);
                    })
                    ->orWhereHas('childChildRequest', function ($q) {
                        $q->whereIn('status_id', [config('constant.APPROVED_STATUS'), config('constant.ASSIGNED_STATUS')]);

                    })
                    ->orderBy('start_datetime', 'desc')
                    ->get();

            }
        }

        return $this->model->with(['status', 'vehicleRequestType', 'office'])
            ->whereIn('status_id', [config('constant.APPROVED_STATUS'), config('constant.ASSIGNED_STATUS')])
            ->whereIn('office_id', $accessibleOfficeIds)
            ->orWhereHas('childChildRequest', function ($q) {
                $q->whereIn('status_id', [config('constant.APPROVED_STATUS'), config('constant.ASSIGNED_STATUS')]);
            })
            ->orderBy('vehicle_request_number', 'desc')
            ->get();
    }

    public function getClosed()
    {
        $authUser = auth()->user();
        $currentOffice = $authUser->getCurrentOffice();
        $accessibleOfficeIds = $authUser->getAccessibleOfficesIds();

        if ($currentOffice) {
            if ($currentOffice->office_type_id == config('constant.HEAD_OFFICE')) {

                return $this->model->with(['status', 'vehicleRequestType'])->select(['*'])
                    ->whereIn('status_id', [config('constant.CLOSED_STATUS')])
                    ->whereIn('office_id', $accessibleOfficeIds)
                    ->orWhere(function ($q) {
                        $q->whereNull('office_id');
                        $q->whereIn('status_id', [config('constant.CLOSED_STATUS')]);
                    })
                    ->orWhereHas('childChildRequest', function ($q) {
                        $q->whereIn('status_id', [config('constant.CLOSED_STATUS')]);

                    })
                    ->orderBy('start_datetime', 'desc')
                    ->get();

            }
        }

        return $this->model->with(['status', 'vehicleRequestType', 'office'])
            ->whereIn('status_id', [config('constant.CLOSED_STATUS')])
            ->whereIn('office_id', $accessibleOfficeIds)
            ->orWhereHas('childChildRequest', function ($q) {
                $q->whereIn('status_id', [config('constant.CLOSED_STATUS')]);
            })
            ->orderBy('vehicle_request_number', 'desc')
            ->get();
    }

    public function approvedVehicleRequests()
    {
        return $this->model->whereIn('status_id', [config('constant.APPROVED_STATUS')]);
    }

    public function approvedAndAssignedVehicleRequests()
    {
        return $this->model->whereIn('status_id', [config('constant.APPROVED_STATUS'), config('constant.ASSIGNED_STATUS')]);
    }

    public function getApprovedAndAssignedVehicleRequests()
    {
        return $this->model->whereIn('status_id', [config('constant.APPROVED_STATUS'), config('constant.ASSIGNED_STATUS')])->get();
    }
    public function getDriverAssigned()
    {
        return $this->model
            ->with(['status', 'assignedVehicle', 'requester', 'office'])
            ->where('driver_id', auth()->id())
            ->where('status_id', config('constant.ASSIGNED_STATUS'))
            ->orderBy('start_datetime', 'desc')
            ->get();
    }

    public function getVehicleRequestsForApproval($authUser)
    {
        return $this->model->whereIn('status_id', [config('constant.SUBMITTED_STATUS'), config('constant.RECOMMENDED_STATUS')])
            ->where('approver_id', '=', $authUser->id)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
    }

    public function getVehicleRequestNumber($fiscalYear)
    {
        $max = $this->model->select(['fiscal_year_id', 'vehicle_request_number'])
            ->where('fiscal_year_id', $fiscalYear->id)
            ->max('vehicle_request_number') + 1;
        return $max;
    }

    public function amend($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $vehicleRequest = $this->model->find($id);
            $vehicleRequest->update(['status_id' => config('constant.AMENDED_STATUS')]);
            $clone = $vehicleRequest->replicate();
            unset($clone->reviewer_id);
            unset($clone->approver_id);
            $clone->status_id = 1;
            $clone->created_by = $inputs['created_by'];
            $clone->modification_vehicle_request_id = $vehicleRequest->id;
            $parentVehicleRequestId = $vehicleRequest->modification_vehicle_request_id ?: $vehicleRequest->id;
            $clone->modification_number = $this->model->where('modification_vehicle_request_id', $parentVehicleRequestId)
                ->max('modification_number') + 1;
            $clone->save();

            DB::commit();
            return $clone;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }

    public function approve($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $vehicleRequest = $this->model->find($id);
            if ($inputs['status_id'] == config('constant.RECOMMENDED_STATUS')) {
                $inputs['approver_id'] = $inputs['recommended_to'];
                $inputs['reviewer_id'] = $vehicleRequest->approver_id;
            }
            $vehicleRequest->update($inputs);
            $vehicleRequest->logs()->create($inputs);
            DB::commit();
            return $vehicleRequest;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }

    public function assign($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $vehicleRequest = $this->model->find($id);
            if ($inputs['status_id'] == config('constant.APPROVED_STATUS')) {
                $inputs['assigned_departure_datetime'] = $vehicleRequest->start_datetime->format('Y-m-d H:i');
                $inputs['assigned_arrival_datetime'] = $vehicleRequest->start_datetime->endOfDay()->format('Y-m-d H:i');
                $inputs['status_id'] = config('constant.ASSIGNED_STATUS');
            }
            if (isset($inputs['driver_id'])) {
                $vehicleRequest->driver_id = $inputs['driver_id'];
            }
            $vehicleRequest->update($inputs);
            $vehicleRequest->logs()->create($inputs);
            DB::commit();
            return $vehicleRequest;
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
            $vehicleRequest = $this->model->create($inputs);
            if (!empty($inputs['procurement_officer']) && $vehicleRequest->vehicle_request_type_id == 2) {
                $vehicleRequest->procurementOfficers()->sync($inputs['procurement_officer']);
            }
            if ($inputs['btn'] == 'submit') {
                $forwardInputs = [
                    'request_date' => date('Y-m-d'),
                    'user_id' => $inputs['created_by'],
                    'log_remarks' => 'Vehicle request is submitted.',
                    'original_user_id' => $inputs['original_user_id'],
                ];
                $vehicleRequest = $this->forward($vehicleRequest->id, $forwardInputs);
            }

            DB::commit();
            return $vehicleRequest;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }

    public function destroy($id)
    {
        try {
            $vehicleRequest = $this->model->findOrFail($id);
            if ($vehicleRequest->parentVehicleRequest) {
                $parentVehicleRequest = $vehicleRequest->parentVehicleRequest;
                if ($parentVehicleRequest->vehicle_request_type_id == 1) {
                    $parentVehicleRequest->update(['status_id' => config('constant.ASSIGNED_STATUS')]);
                } else {
                    $parentVehicleRequest->update(['status_id' => config('constant.APPROVED_STATUS')]);
                }
            }
            $vehicleRequest->procurementOfficers()->sync([]);
            $vehicleRequest->logs()->delete();
            $vehicleRequest->delete();
            return true;
        } catch (\Illuminate\Database\QueryException $e) {
            return false;
        }
    }

    public function forward($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $vehicleRequest = $this->model->findOrFail($id);
            $inputs['status_id'] = config('constant.SUBMITTED_STATUS');
            if (!$vehicleRequest->vehicle_request_number) {
                $fiscalYear = $this->fiscalYear->where('start_date', '<=', date('Y-m-d'))
                    ->where('end_date', '>=', date('Y-m-d'))
                    ->first();

                $inputs['fiscal_year_id'] = $fiscalYear->id;
                $inputs['prefix'] = 'VE';
                $inputs['vehicle_request_number'] = $this->getVehicleRequestNumber($fiscalYear);
            }
            $vehicleRequest->update($inputs);
            $vehicleRequest->logs()->create($inputs);
            DB::commit();
            return $vehicleRequest;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }

    public function update($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $vehicleRequest = $this->model->find($id);
            $vehicleRequest->fill($inputs)->save();
            if (!empty($inputs['procurement_officer']) && $vehicleRequest->vehicle_request_type_id == 2) {
                $vehicleRequest->procurementOfficers()->sync($inputs['procurement_officer']);
            }
            if ($inputs['btn'] == 'submit') {
                $forwardInputs = [
                    'request_date' => date('Y-m-d'),
                    'user_id' => $inputs['updated_by'],
                    'log_remarks' => 'Vehicle request is submitted.',
                    'original_user_id' => $inputs['original_user_id'],
                ];
                $vehicleRequest = $this->forward($vehicleRequest->id, $forwardInputs);
            }

            DB::commit();
            return $vehicleRequest;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }

    public function close($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $vehicleRequest = $this->model->find($id);
            $inputs['status_id'] = config('constant.CLOSED_STATUS');
            $inputs['updated_by'] = auth()->user()->id;
            $inputs['closed_at'] = date('Y-m-d H:i:s');
            $vehicleRequest->update($inputs);
            $inputs['user_id'] = $inputs['updated_by'];
            $inputs['log_remarks'] = 'Vehicle Request closed.';
            $vehicleRequest->logs()->create($inputs);
            DB::commit();
            return $vehicleRequest;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            return false;
        }
    }

}
