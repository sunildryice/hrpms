<?php

namespace Modules\TravelRequest\Repositories;

use App\Repositories\Repository;
use Modules\Master\Models\FiscalYear;
use Modules\TravelRequest\Models\LocalTravel;

use DB;

class LocalTravelRepository extends Repository
{
    private $fiscalYear;
    public function __construct(
        FiscalYear $fiscalYear,
        LocalTravel $localTravel
    ){
        $this->fiscalYear = $fiscalYear;
        $this->model = $localTravel;
    }

    public function getApproved()
    {
        $authUser = auth()->user();
        $currentOffice = $authUser->getCurrentOffice();
        $accessibleOfficeIds = $authUser->getAccessibleOfficesIds();
        $query = $this->model->with(['travelRequest','travelRequest.fiscalYear', 'status', 'requester', 'submittedLog', 'fiscalYear'])
            ->select(['*']);


        if ($currentOffice) {
            if ($currentOffice->office_type_id == config('constant.HEAD_OFFICE')) {
                return $query
                    ->whereIn('status_id', [config('constant.APPROVED_STATUS')])
                    ->whereIn('office_id', $accessibleOfficeIds)
                    ->orWhere(function ($q) {
                        $q->whereNull('office_id');
                        $q->whereIn('status_id', [config('constant.APPROVED_STATUS')]);
                    });
            }
        }

        return $query
            ->whereIn('status_id', [config('constant.APPROVED_STATUS')])
            ->whereIn('office_id', $accessibleOfficeIds);
    }

    public function getTravelNumber($fiscalYear)
    {
        $max = $this->model->select(['fiscal_year_id', 'local_travel_number'])
                ->where('fiscal_year_id', $fiscalYear->id)
                ->max('local_travel_number') + 1;
        return $max;
    }

    public function getLocalTravelsForReviewAndApproval($authUser)
    {
        return $this->model->select(['*'])
            ->where(function ($q) use ($authUser){
                $q->where('status_id', config('constant.SUBMITTED_STATUS'))
                    ->where('approver_id', $authUser->id);
            })->orWhere(function ($q) use ($authUser){
                $q->whereIn('status_id', [config('constant.RECOMMENDED_STATUS')])
                    ->where('approver_id', $authUser->id);
            })->orderBy('created_at', 'desc')
            ->take(5)->get();
    }

    public function approve($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $localTravel = $this->model->find($id);
            if($inputs['status_id'] == config('constant.RECOMMENDED_STATUS')){
                $inputs['approver_id'] = $inputs['recommended_to'];
                $inputs['reviewer_id'] = $localTravel->approver_id;
            }
            $localTravel->update($inputs);
            $localTravel->logs()->create($inputs);
            DB::commit();
            return $localTravel;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }

    public function pay($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $localTravel = $this->model->find($id);
            $inputs['log_remarks'] = $inputs['payment_remarks'];
            $inputs['status_id'] = config('constant.PAID_STATUS');
            $localTravel->update($inputs);
            $localTravel->logs()->create($inputs);
            DB::commit();
            return $localTravel;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }


    public function create($inputs)
    {
        DB::beginTransaction();
        try {
            $localTravel = $this->model->create($inputs);
            DB::commit();
            return $localTravel;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }

    public function destroy($id)
    {
        try {
            $localTravel = $this->model->findOrFail($id);
            $localTravel->localTravelItineraries()->delete();
            $localTravel->logs()->delete();
            $localTravel->delete();
            return true;
        } catch (\Illuminate\Database\QueryException $e) {
            return false;
        }
    }

    public function forward($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $localTravel = $this->model->findOrFail($id);
            $inputs['status_id'] = config('constant.SUBMITTED_STATUS');
            if(!$localTravel->local_travel_number){
                $fiscalYear = $this->fiscalYear->where('start_date', '<=', date('Y-m-d'))
                    ->where('end_date', '>=', date('Y-m-d'))
                    ->first();

                $inputs['request_date'] = date('Y-m-d');
                $inputs['fiscal_year_id'] = $fiscalYear->id;
                $inputs['prefix'] = 'L-TRF';
                $inputs['local_travel_number'] = $this->getTravelNumber($fiscalYear);
            }
            $localTravel->update($inputs);
            $localTravel->logs()->create($inputs);
            DB::commit();
            return $localTravel;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }

    public function update($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $localTravel = $this->model->find($id);
            $localTravel->fill($inputs)->save();

            if ($inputs['btn'] == 'submit') {
                $forwardInputs = [
                    'user_id' => $inputs['updated_by'],
                    'log_remarks' => 'Local travel reimbursement is submitted.',
                    'original_user_id' => $inputs['original_user_id'],
                ];
                $localTravel = $this->forward($localTravel->id, $forwardInputs);
            }

            DB::commit();
            return $localTravel;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }
}
