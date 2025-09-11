<?php

namespace Modules\TravelRequest\Repositories;

use App\Repositories\Repository;
use DB;
use Modules\Master\Repositories\FiscalYearRepository;
use Modules\TravelRequest\Models\TravelRequest;

class TravelRequestRepository extends Repository
{
    private $fiscalYears;

    public function __construct(
        FiscalYearRepository $fiscalYears,
        TravelRequest $travelRequest
    ) {
        $this->fiscalYears = $fiscalYears;
        $this->model = $travelRequest;
    }

    public function getApproved()
    {
        $authUser = auth()->user();
        $currentOffice = $authUser->getCurrentOffice();
        $accessibleOfficeIds = $authUser->getAccessibleOfficesIds();

        if ($currentOffice) {
            if ($currentOffice->office_type_id == config('constant.HEAD_OFFICE')) {
                return $this->model->with(['requester', 'status', 'fiscalYear'])->select(['*'])
                    ->whereIn('status_id', [config('constant.APPROVED_STATUS'), config('constant.AMENDED_STATUS')])
                    ->whereIn('office_id', $accessibleOfficeIds)
                    ->orWhere(function ($q) {
                        $q->whereNull('office_id');
                        $q->whereIn('status_id', [config('constant.APPROVED_STATUS'), config('constant.AMENDED_STATUS')]);
                    })
                    ->orderBy('departure_date', 'desc')
                    ->get();
            }
        }

        return $this->model->with(['requester', 'status', 'fiscalYear'])->select(['*'])
            ->whereIn('status_id', [config('constant.APPROVED_STATUS'), config('constant.AMENDED_STATUS')])
            ->whereIn('office_id', $accessibleOfficeIds)
            ->orderBy('departure_date', 'desc')
            ->get();

        // return $this->model->with(['requester','status'])->select(['*'])
        //         ->whereIn('status_id', [config('constant.APPROVED_STATUS'), config('constant.AMENDED_STATUS')])
        //         ->orderBy('departure_date', 'desc')
        //         ->get();
    }

    public function getTravelNumber($fiscalYearId)
    {
        $max = $this->model->select(['fiscal_year_id', 'travel_number'])
            ->where('fiscal_year_id', $fiscalYearId)
            ->max('travel_number') + 1;

        return $max;
    }

    public function getTravelRequestsForApproval($authUser)
    {
        return $this->model->whereIn('status_id', [config('constant.SUBMITTED_STATUS'), config('constant.RECOMMENDED_STATUS')])
            ->where('approver_id', '=', $authUser->id)
            ->orderBy('request_date', 'desc')
            ->take(5)
            ->get();
    }

    public function getEmployeesOnTravel()
    {
        return $this->model->with('requester.employee')->whereIn('status_id', [config('constant.APPROVED_STATUS')])
            ->where('departure_date', '<=', now()->format('Y-m-d'))
            ->where('return_date', '>=', now()->format('Y-m-d'))
            ->with(['requester'])
            ->get();
    }

    public function amend($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $travelRequest = $this->model->find($id);
            $travelRequest->update(['status_id' => config('constant.AMENDED_STATUS')]);
            $clone = $travelRequest->replicate();
            unset($clone->reviewer_id);
            unset($clone->approver_id);
            $clone->status_id = 1;
            $clone->request_date = date('Y-m-d');
            $clone->created_by = $inputs['created_by'];
            $clone->modification_travel_request_id = $travelRequest->id;
            $parentTravelRequestId = $travelRequest->modification_travel_request_id ?: $travelRequest->id;
            $clone->modification_number = $this->model->where('modification_travel_request_id', $parentTravelRequestId)
                ->max('modification_number') + 1;
            $clone->save();

            foreach ($travelRequest->travelRequestItineraries as $itinerary) {
                $travelModes = $itinerary->travelModes()->pluck('id')->toArray();
                unset($itinerary->id);
                unset($itinerary->travel_request_id);
                unset($itinerary->created_at);
                unset($itinerary->updated_at);
                $itineraryInputs = $itinerary->toArray();
                $itineraryInputs['created_by'] = $inputs['created_by'];
                $itineraryInputs['departure_date'] = $itinerary->departure_date;
                $itineraryInputs['arrival_date'] = $itinerary->arrival_date;
                $cloneItinerary = $clone->travelRequestItineraries()->create($itineraryInputs);
                if ($travelModes) {
                    $cloneItinerary->travelModes()->sync($travelModes);
                }
            }

            if ($travelRequest->travelRequestEstimate) {
                $estimate = $travelRequest->travelRequestEstimate;
                unset($estimate->id);
                unset($estimate->travel_request_id);
                $estimateInputs = $estimate->toArray();
                $estimateInputs['created_by'] = $inputs['created_by'];
                $clone->travelRequestEstimate()->create($estimateInputs);
            }

            if ($travelRequest->accompanyingStaffs) {
                $accompanyingStaffs = $travelRequest->accompanyingStaffs->pluck('id')->toArray();
                $clone->accompanyingStaffs()->sync($accompanyingStaffs);
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
            $travelRequest = $this->model->find($id);
            if ($inputs['status_id'] == config('constant.RECOMMENDED_STATUS')) {
                $inputs['approver_id'] = $inputs['recommended_to'];
                $inputs['reviewer_id'] = $travelRequest->approver_id;
            }
            $travelRequest->update($inputs);

            if ($travelRequest->parentTravelRequest) {
                $parentTravelRequest = $travelRequest->parentTravelRequest;
                if ($parentTravelRequest->travelReport) {
                    $parentTravelRequest->travelReport->update(['travel_request_id' => $travelRequest->id]);
                }
            }

            $travelRequest->logs()->create($inputs);
            DB::commit();

            return $travelRequest;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();

            return false;
        }
    }

    public function cancel($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $travelRequest = $this->model->find($id);
            $travelRequest->update($inputs);
            $travelRequest->logs()->create($inputs);
            DB::commit();

            return $travelRequest;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();

            return false;
        }
    }

    public function create($inputs)
    {
        DB::beginTransaction();
        try {
            $travelRequest = $this->model->create($inputs);
            if (! empty($inputs['accompanying_staff'])) {
                $travelRequest->accompanyingStaffs()->sync($inputs['accompanying_staff']);
            }
            if (array_key_exists('substitutes', $inputs)) {
                $travelRequest->substitutes()->sync($inputs['substitutes']);
            }
            DB::commit();

            return $travelRequest;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();

            return false;
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $travelRequest = $this->model->findOrFail($id);
            if ($travelRequest->parentTravelRequest) {
                $parentTravelRequest = $travelRequest->parentTravelRequest;
                $parentTravelRequest->update(['status_id' => config('constant.APPROVED_STATUS')]);
            }
            $travelRequest->accompanyingStaffs()->sync([]);
            $travelRequest->travelRequestEstimate()->delete();
            $travelRequest->travelRequestItineraries()->delete();
            $travelRequest->logs()->delete();
            $travelRequest->delete();
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
            $travelRequest = $this->model->findOrFail($id);
            $inputs['status_id'] = config('constant.SUBMITTED_STATUS');
            if (! $travelRequest->travel_number) {
                $fiscalYear = $this->fiscalYears->where('start_date', '<=', date('Y-m-d'))
                    ->where('end_date', '>=', date('Y-m-d'))
                    ->first();

                $inputs['fiscal_year_id'] = $fiscalYear->id;
                $inputs['request_date'] = date('Y-m-d');
                $inputs['prefix'] = 'TR';
                $inputs['travel_number'] = $this->getTravelNumber($fiscalYear->id);
            }

            $travelRequest->update($inputs);
            $travelRequest->logs()->create($inputs);
            DB::commit();

            return $travelRequest;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();

            return false;
        }
    }

    public function update($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $travelRequest = $this->model->find($id);
            $travelRequest->fill($inputs)->save();
            if (! empty($inputs['accompanying_staff'])) {
                $travelRequest->accompanyingStaffs()->sync($inputs['accompanying_staff']);
            } else {
                $travelRequest->accompanyingStaffs()->sync([]);
            }

            if (array_key_exists('substitutes', $inputs)) {
                $travelRequest->substitutes()->sync($inputs['substitutes']);
            } else {
                $travelRequest->substitutes()->sync([]);
            }
            if ($inputs['btn'] == 'submit') {
                $forwardInputs = [
                    'user_id' => $inputs['updated_by'],
                    'log_remarks' => 'Travel request is submitted.',
                    'original_user_id' => $inputs['original_user_id'],
                ];
                $travelRequest = $this->forward($travelRequest->id, $forwardInputs);
            }

            DB::commit();

            return $travelRequest;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();

            return false;
        }
    }

    public function getUpcomingTravels()
    {
        $now = date('Y-m-d');
        $futureDate = now()->addDays(7)->format('Y-m-d');

        return $this->model->with(['requester.employee'])
            ->where('departure_date', '>', $now)
            ->whereBetween('departure_date', [$now, $futureDate])
            ->where('status_id', config('constant.APPROVED_STATUS'))
            // ->whereColumn('requester_id',gcc )
            ->with(['requester'])
            ->get();
    }

    public function storeAdvance($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $travelRequest = $this->model->find($id);
            $travelRequest->fill($inputs)->save();
            DB::commit();

            return $travelRequest;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();

            return false;
        }
    }
}
