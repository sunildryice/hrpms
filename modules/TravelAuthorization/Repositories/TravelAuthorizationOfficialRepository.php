<?php

namespace Modules\TravelAuthorization\Repositories;

use App\Repositories\Repository;

use DB;
use Modules\TravelAuthorization\Models\TravelAuthorizationOfficial;

class TravelAuthorizationOfficialRepository extends Repository
{
    public function __construct(
        TravelAuthorizationOfficial $travel
    )
    {
        $this->model = $travel;
    }

    public function updateTotalDsa($travel)
    {
        // $overnights = $travel->getOvernights();
        // if($overnights){
        //     $inputs['dsa_total_price'] = $overnights * $travel->dsa_unit_price;
        // } else {
        //     $inputs['dsa_total_price'] = ($travel->arrival_date == $travel->travelAuthorization->departure_date) ? $travel->dsa_unit_price/2 : 0;
        // }
        // return $travel->fill($inputs)->save();

        $inputs['dsa_total_price'] = $this->calculateTotalDsaAmountForItinerary($travel);
        return $travel->fill($inputs)->save();
    }

    public function calculateTotalDsaAmountForItinerary($travel)
    {
        $itinerary = $travel;
        $travel = $itinerary->travelAuthorization;

        $arrivalDate = $itinerary->arrival_date;
        $departureDate = $itinerary->departure_date;
        $dsaRate = $itinerary->dsa_unit_price;

        // Calculate the DSA amount for each date
        $dsaAmount = 0;
        $currentDate = $departureDate;
        while ($currentDate <= $arrivalDate) {
            // Check if the current date is the last date and if it matches the travel request's return date
            $isLastDate = ($currentDate == $travel->return_date);

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

}
