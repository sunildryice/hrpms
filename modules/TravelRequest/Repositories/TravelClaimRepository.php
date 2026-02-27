<?php

namespace Modules\TravelRequest\Repositories;

use App\Repositories\Repository;
use Modules\TravelRequest\Models\TravelClaim;

use DB;
use Modules\TravelRequest\Models\TravelRequest;

class TravelClaimRepository extends Repository
{
    public function __construct(
        TravelClaim $travelClaim,
        protected TravelRequest $travelRequest
    ) {
        $this->model = $travelClaim;
    }

    public function approve($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $travelClaim = $this->model->find($id);
            if ($inputs['status_id'] == config('constant.RECOMMENDED2_STATUS')) {
                $inputs['recommender_id'] = $travelClaim->approver_id;
                $inputs['approver_id'] = $inputs['recommended_to'];
            }
            $travelClaim->update($inputs);
            $travelClaim->logs()->create($inputs);
            DB::commit();
            return $travelClaim;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }

    public function pay($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $travelClaim = $this->model->find($id);
            $inputs['log_remarks'] = $inputs['payment_remarks'];
            $inputs['status_id'] = config('constant.PAID_STATUS');
            $travelClaim->update($inputs);
            $travelClaim->logs()->create($inputs);
            DB::commit();
            return $travelClaim;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }



    public function createClaim($travelRequestId, $authId)
    {

        DB::beginTransaction();
        try {
            $travelRequest = $this->travelRequest->find($travelRequestId);
            $advanceAmount = $travelRequest->travelRequestEstimate?->total_amount ?? 0;

            $travelClaim = $this->model->create([
                'travel_request_id' => $travelRequestId,
                'advance_amount' => $advanceAmount,
                'created_by' => $authId,
                'status_id' => 1
            ]);
            $this->updateTotalAmount($travelClaim->id);

            $startDate = \Carbon\Carbon::parse($travelRequest->departure_date);
            $endDate = \Carbon\Carbon::parse($travelRequest->return_date);
            $period = \Carbon\CarbonPeriod::create($startDate, '1 day', $endDate);
            $itineraries = $travelRequest->travelRequestDayItineraries;

            foreach ($period as $date){
                $travelItinerary = $itineraries->filter(function ($itinerary) use ($date) {
                    return $itinerary->date->format('Y-m-d') == $date->format('Y-m-d');
                })->first();
                $travelClaim->dsaClaims()->create([
                    'activity_code_id'=>$travelItinerary->activity_id,
                    'activities'=>$travelItinerary->planned_activities,
                    'departure_date'=>$travelItinerary->date,
                    'created_by'=>$authId,
                ]);
            }
            DB::commit();
            return $travelClaim;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $travelClaim = $this->model->findOrFail($id);
            $travelClaim->logs()->delete();
            $travelClaim->attachments()->delete();
            $travelClaim->expenses()->delete();
            $travelClaim->dsaClaim()->delete();
            $travelClaim->delete();
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
            $travelClaim = $this->model->findOrFail($id);
            $inputs['status_id'] = config('constant.SUBMITTED_STATUS');
            $travelClaim->update($inputs);
            $travelClaim->logs()->create($inputs);
            DB::commit();
            return $travelClaim;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }

    public function review($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $travelClaim = $this->model->find($id);
            $travelClaim->update($inputs);
            $travelClaim->logs()->create($inputs);
            DB::commit();
            return $travelClaim;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }

    public function update($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $travelClaim = $this->model->findOrFail($id);
            $travelClaim->fill($inputs)->save();
            if ($inputs['btn'] == 'submit') {
                $forwardInputs = [
                    'user_id' => $inputs['updated_by'],
                    'log_remarks' => 'Travel claim is submitted.',
                    'original_user_id' => $inputs['original_user_id'],
                ];
                $travelClaim = $this->forward($travelClaim->id, $forwardInputs);
            }
            $this->updateTotalAmount($id);
            DB::commit();
            return $travelClaim;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }

    public function updateTotalAmount($claimId)
    {
        DB::beginTransaction();
        try {
            $travelClaim = $this->model->find($claimId);

            $expenseAmount = $travelClaim->expenses->sum('expense_amount');
            $dsaAmount = $travelClaim->dsaClaim->sum('total_amount');
            $localTravelAmount = $travelClaim->localTravels->sum('travel_fare');
            $totalClaimed = $expenseAmount + $dsaAmount + $localTravelAmount;

            $updateInputs = [
                'total_expense_amount' => $expenseAmount,
                'total_itinerary_amount' => $dsaAmount,
                'total_amount' => $totalClaimed,
                'refundable_amount' => $totalClaimed - $travelClaim->advance_amount,
            ];

            $travelClaim->update($updateInputs);
            DB::commit();
            return $travelClaim;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }

}
