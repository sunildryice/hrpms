<?php

namespace Modules\TravelRequest\Repositories;

use App\Repositories\Repository;
use Modules\TravelRequest\Models\TravelRequestDayItinerary;
use DB;

class TravelRequestDayItineraryRepository extends Repository
{
    public function __construct(
        TravelRequestDayItinerary $travelRequestDayItinerary
    ) {
        $this->model = $travelRequestDayItinerary;
    }

    /**
     * Get all day itineraries for a travel request (with default ordering)
     *
     * @param int $travelRequestId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getByTravelRequest($travelRequestId)
    {
        return $this->model
            ->where('travel_request_id', $travelRequestId)
            ->orderBy('date', 'asc')
            ->get();
    }

    /**
     * Create a new day-wise itinerary entry
     *
     * @param array $inputs
     * @return TravelRequestDayItinerary|bool
     */
    public function create($inputs)
    {
        DB::beginTransaction();
        try {
            $dayItinerary = $this->model->create($inputs);

            DB::commit();
            return $dayItinerary;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }

    /**
     * Update an existing day-wise itinerary entry
     *
     * @param int $id
     * @param array $inputs
     * @return TravelRequestDayItinerary|bool
     */
    public function update($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $dayItinerary = $this->model->findOrFail($id);
            $dayItinerary->fill($inputs)->save();

            DB::commit();
            return $dayItinerary;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }

    /**
     * Bulk create or update multiple day itineraries for a travel request
     * (most common use-case when saving from frontend array)
     *
     * @param int $travelRequestId
     * @param array $dayItinerariesData  // array of arrays, each with date, activities, etc.
     * @param int $userId (creator/updater)
     * @return bool
     */
    public function syncDayItineraries($travelRequestId, array $dayItinerariesData, $userId)
    {
        DB::beginTransaction();
        try {
            // Optional: clear existing entries first (full replace)
            // $this->model->where('travel_request_id', $travelRequestId)->delete();

            foreach ($dayItinerariesData as $data) {
                $this->model->updateOrCreate(
                    [
                        'travel_request_id' => $travelRequestId,
                        'date' => $data['date'],
                    ],
                    [
                        'planned_activities' => $data['activities'] ?? null,
                        'accommodation' => $data['accommodation'] ?? false,
                        'air_ticket' => $data['air_ticket'] ?? false,
                        'departure_place' => $data['from'] ?? null,
                        'arrival_place' => $data['to'] ?? null,
                        'departure_time' => $data['departure_time'] ?? null,
                        'vehicle' => $data['vehicle'] ?? false,
                        'vehicle_request_form_link' => $data['vehicle_request_form_link'] ?? null,
                        'created_by' => $userId,
                        'updated_by' => $userId,
                    ]
                );
            }

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            return false;
        }
    }

    /**
     * Delete all day itineraries for a travel request
     * (useful when resetting or replacing entire set)
     *
     * @param int $travelRequestId
     * @return bool
     */
    public function deleteByTravelRequest($travelRequestId)
    {
        return $this->model
            ->where('travel_request_id', $travelRequestId)
            ->delete() > 0;
    }
}