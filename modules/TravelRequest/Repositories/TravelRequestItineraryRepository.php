<?php

namespace Modules\TravelRequest\Repositories;

use App\Repositories\Repository;
use Modules\TravelRequest\Models\TravelRequestItinerary;

use DB;

class TravelRequestItineraryRepository extends Repository
{
    public function __construct(
        TravelRequestItinerary $travelRequestItinerary
    )
    {
        $this->model = $travelRequestItinerary;
    }

    public function create($inputs)
    {
        DB::beginTransaction();
        try {
            $travelRequestItinerary = $this->model->create($inputs);
            if(array_key_exists('travel_modes', $inputs)){
                $travelRequestItinerary->travelModes()->sync($inputs['travel_modes']);
            }
            // $this->updateTotalDsa($travelRequestItinerary);
            // $this->updateTravelEstimateDsa($travelRequestItinerary->travelRequest);
            DB::commit();
            return $travelRequestItinerary;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }

    public function update($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $travelRequestItinerary = $this->model->findOrFail($id);
            $travelRequestItinerary->fill($inputs)->save();
            if(array_key_exists('travel_modes', $inputs)){
                $travelRequestItinerary->travelModes()->sync($inputs['travel_modes']);
            } else {
                $travelRequestItinerary->travelModes()->sync([]);
            }

            // $this->updateTotalDsa($travelRequestItinerary);
            // $this->updateTravelEstimateDsa($travelRequestItinerary->travelRequest);
            DB::commit();
            return $travelRequestItinerary;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }

    public function updateTotalDsa($travelRequestItinerary)
    {
        // $overnights = $travelRequestItinerary->getOvernights();
        // if($overnights){
        //     $inputs['dsa_total_price'] = $overnights * $travelRequestItinerary->dsa_unit_price;
        // } else {
        //     $inputs['dsa_total_price'] = ($travelRequestItinerary->arrival_date == $travelRequestItinerary->travelRequest->departure_date) ? $travelRequestItinerary->dsa_unit_price/2 : 0;
        // }
        // return $travelRequestItinerary->fill($inputs)->save();

        $inputs['dsa_total_price'] = $this->calculateTotalDsaAmountForItinerary($travelRequestItinerary);
        return $travelRequestItinerary->fill($inputs)->save();
    }

    public function calculateTotalDsaAmountForItinerary($travelRequestItinerary)
    {
        $itinerary = $travelRequestItinerary;
        $travelRequest = $itinerary->travelRequest;

        $arrivalDate = $itinerary->arrival_date;
        $departureDate = $itinerary->departure_date;
        $dsaRate = $itinerary->dsa_unit_price;

        // Calculate the DSA amount for each date
        $dsaAmount = 0;
        $currentDate = $departureDate;
        while ($currentDate <= $arrivalDate) {
            // Check if the current date is the last date and if it matches the travel request's return date
            $isLastDate = ($currentDate == $travelRequest->return_date);

            // Calculate the DSA amount for the current date based on the rate (50% for last date if applicable)
            $currentDsaRate = ($isLastDate) ? 0.5 * $dsaRate : $dsaRate;

            // Increment the DSA amount by the daily rate
            $dsaAmount += $currentDsaRate;

            // Move to the next date
            $currentDate->addDay();
        }

        // Return the calculated DSA amount for this itinerary
        return $dsaAmount;
    }

    public function updateTravelEstimateDsa($travelRequest)
    {
        if($travelRequest->travelRequestEstimate()->exists()){
            return $travelRequest->travelRequestEstimate->fill(['estimated_dsa' => $travelRequest->travelRequestItineraries->sum('dsa_total_price')])->save();
        }
    }

}
