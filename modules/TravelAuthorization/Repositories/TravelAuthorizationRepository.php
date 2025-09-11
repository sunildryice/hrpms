<?php

namespace Modules\TravelAuthorization\Repositories;

use App\Repositories\Repository;
use DB;
use Modules\Master\Repositories\FiscalYearRepository;
use Modules\TravelAuthorization\Models\TravelAuthorization;

class TravelAuthorizationRepository extends Repository
{
    private $fiscalYears;

    public function __construct(
        FiscalYearRepository $fiscalYears,
        TravelAuthorization $travel
    ) {
        $this->fiscalYears = $fiscalYears;
        $this->model = $travel;
    }

    public function getTaNumber($fiscalYearId)
    {
        $max = $this->model->select(['fiscal_year_id', 'ta_number'])
            ->where('fiscal_year_id', $fiscalYearId)
            ->max('ta_number') + 1;
        return $max;
    }

    public function getTravelAuthorizationsForApproval($authUser)
    {
        return $this->model->whereIn('status_id', [config('constant.SUBMITTED_STATUS'), config('constant.RECOMMENDED_STATUS')])
            ->where('approver_id', '=', $authUser->id)
            ->orderBy('request_date', 'desc')
            ->take(5)
            ->get();
    }

    public function getEmployeesOnTravel()
    {
        return $this->model->whereIn('status_id', [config('constant.APPROVED_STATUS')])
            ->where('departure_date', '<=', now()->format('Y-m-d'))
            ->where('return_date', '>=', now()->format('Y-m-d'))
            ->with(['requester'])
            ->get();
    }

    public function amend($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $travel = $this->model->find($id);
            $travel->update(['status_id' => config('constant.AMENDED_STATUS')]);
            $clone = $travel->replicate();
            unset($clone->reviewer_id);
            unset($clone->approver_id);
            $clone->status_id = 1;
            $clone->request_date = date('Y-m-d');
            $clone->created_by = $inputs['created_by'];
            $clone->modification_ta_request_id = $travel->id;
            $parentRequestId = $travel->modification_ta_request_id ?: $travel->id;
            $clone->modification_number = $this->model->where('modification_ta_request_id', $parentRequestId)
                ->max('modification_number') + 1;
            $clone->save();
            foreach ($travel->officials as $official) {
                $officialClone = $official->replicate();
                $officialClone->travel_authorization_id = $clone->id;
                $officialClone->created_by = $official->updated_by = $inputs['created_by'];
                $officialClone->save();
            }
            foreach ($travel->itineraries as $itinerary) {
                $itineraryClone = $itinerary->replicate();
                $itineraryClone->travel_authorization_id= $clone->id;
                $itineraryClone->created_by = $itinerary->updated_by = $inputs['created_by'];
                $itineraryClone->save();
            }

            foreach ($travel->estimates as $estimate) {
                $estimateClone = $estimate->replicate();
                $estimateClone->travel_authorization_id = $clone->id;
                $estimateClone->created_by = $estimate->updated_by = $inputs['created_by'];
                $estimateClone->save();
            }

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
            $travel = $this->model->find($id);
            if ($inputs['status_id'] == config('constant.RECOMMENDED_STATUS')) {
                $inputs['approver_id'] = $inputs['recommended_to'];
                $inputs['recommender_id'] = $travel->approver_id;
            }
            $travel->update($inputs);

            $travel->logs()->create($inputs);
            DB::commit();
            return $travel;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }

    public function cancel($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $travel = $this->model->find($id);
            $travel->update($inputs);
            $travel->logs()->create($inputs);
            DB::commit();
            return $travel;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $travel = $this->model->findOrFail($id);
            if ($travel->parentRequest) {
                $parentRequest = $travel->parentRequest;
                $parentRequest->update(['status_id' => config('constant.APPROVED_STATUS')]);
            }
            $travel->estimates()->delete();
            $travel->itineraries()->delete();
            $travel->officials()->delete();
            $travel->logs()->delete();
            $travel->delete();
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
            $travel = $this->model->findOrFail($id);
            $inputs['status_id'] = config('constant.SUBMITTED_STATUS');
            if (!$travel->ta_number) {
                $fiscalYear = $this->fiscalYears->where('start_date', '<=', date('Y-m-d'))
                    ->where('end_date', '>=', date('Y-m-d'))
                    ->first();

                $inputs['fiscal_year_id'] = $fiscalYear->id;
                $inputs['request_date'] = date('Y-m-d');
                $inputs['prefix'] = 'TAR';
                $inputs['ta_number'] = $this->getTaNumber($fiscalYear->id);
            }

            $travel->update($inputs);
            $travel->logs()->create($inputs);
            DB::commit();
            return $travel;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }

    public function update($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $travel = $this->model->find($id);
            $travel->fill($inputs)->save();
            if ($inputs['btn'] == 'submit') {
                $forwardInputs = [
                    'user_id' => $inputs['updated_by'],
                    'log_remarks' => 'Travel Authorization request is submitted.',
                    'original_user_id' => $inputs['original_user_id'],
                ];
                $travel = $this->forward($travel->id, $forwardInputs);
            }
            DB::commit();
            return $travel;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }

    public function getUpcomingTravels()
    {
        $now = date('Y-m-d');
        $futureDate = now()->addDays(7)->format('Y-m-d');
        return $this->model
            ->where('departure_date', '>', $now)
            ->whereBetween('departure_date', [$now, $futureDate])
            ->where('status_id', config('constant.APPROVED_STATUS'))
            ->with(['requester'])
            ->get();
    }
}
