<?php

namespace Modules\TravelRequest\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use Modules\TravelRequest\Models\TravelRequest;
use Modules\TravelRequest\Models\TravelRequestDayItinerary;
use Modules\TravelRequest\Repositories\TravelRequestRepository;
use Modules\TravelRequest\Requests\TravelRequestDayItinerary\StoreRequest;
use Modules\TravelRequest\Repositories\TravelRequestDayItineraryRepository;
use Modules\TravelRequest\Requests\TravelRequestDayItinerary\UpdateRequest;

class TravelRequestDayItineraryController extends Controller
{
    protected $travelRequest;
    protected $dayItinerary;

    public function __construct(
        TravelRequestRepository $travelRequest,
        TravelRequestDayItineraryRepository $dayItinerary
    ) {
        $this->travelRequest = $travelRequest;
        $this->dayItinerary = $dayItinerary;
    }

    /**
     * Display listing of day-wise itineraries (for DataTables AJAX)
     */
    public function index(Request $request, $travelRequestId)
    {
        $travelRequest = TravelRequest::findOrFail($travelRequestId);

        if ($request->ajax() || $request->expectsJson()) {
            $query = $travelRequest->travelRequestDayItineraries();

            if ($request->boolean('all')) {
                return response()->json([
                    'data' => $query->get()->map(function ($row) {
                        return [
                            'id' => $row->id,
                            'date' => $row->date?->format('Y-m-d'),
                            'planned_activities' => $row->planned_activities,
                            'accommodation' => (bool) $row->accommodation,
                            'air_ticket' => (bool) $row->air_ticket,
                            'vehicle' => (bool) $row->vehicle,
                            'departure_place' => $row->departure_place,
                            'arrival_place' => $row->arrival_place,
                            'departure_time' => $row->departure_time,
                        ];
                    })->toArray()
                ]);
            }

            $datatable = DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('date', fn($row) => $row->date?->format('d M Y'))
                ->addColumn('planned_activities', fn($row) => $row->planned_activities ?: '<em class="text-muted">No activities</em>')
                ->addColumn(
                    'accommodation',
                    fn($row) =>
                    (int) $row->accommodation
                    ? '<span class="text fw-bold">Yes</span>'
                    : '<span class="text-muted fw-bold">No</span>'
                )
                ->addColumn('air_ticket', function ($row) {
                    if (!(int) $row->air_ticket) {
                        return '<span class="text-muted fw-bold">No</span>';
                    }

                    $places = '';
                    if ($row->departure_place && $row->arrival_place) {
                        $places = " ({$row->departure_place} To {$row->arrival_place})";
                    } elseif ($row->departure_place) {
                        $places = " (from {$row->departure_place})";
                    } elseif ($row->arrival_place) {
                        $places = " (to {$row->arrival_place})";
                    }

                    $time = $row->departure_time ? " {$row->departure_time}" : '';

                    return '<span class="text fw-bold">Yes</span>' . $places . $time;
                })
                ->addColumn('vehicle', function ($row) {
                    return
                        (int) $row->vehicle
                        ? '<span class="text fw-bold">Yes</span>'
                        : '<span class="text-muted fw-bold">No</span>';
                })
                ->addColumn('action', function ($row) use ($travelRequestId) {
                    // $btn = '<a class="btn btn-outline-primary btn-sm open-day-itinerary-modal-form" 
                    //            href="' . route('travel.requests.day-itinerary.edit', [$travelRequestId, $row->id]) . '" 
                    //            title="Edit Day Itinerary">
                    //            <i class="bi bi-pencil-square"></i>
                    //        </a>';
    
                    // $btn .= ' <a href="javascript:;" class="btn btn-danger btn-sm delete-record" 
                    //              data-href="' . route('travel.requests.day-itinerary.destroy', [$travelRequestId, $row->id]) . '" 
                    //              title="Delete Day Itinerary">
                    //              <i class="bi bi-trash"></i>
                    //          </a>';
    
                    // return $btn;
                })
                ->rawColumns(['planned_activities', 'accommodation', 'air_ticket', 'vehicle', 'action']);

            return $datatable->make(true);
        }

        abort(403);
    }

    /**
     * Store a newly created day itinerary
     */
    public function store(StoreRequest $request, $travelRequestId)
    {
        $inputs = $request->validated();
        $travelRequest = TravelRequest::findOrFail($travelRequestId);
        $inputs['travel_request_id'] = $travelRequest->id;
        $inputs['created_by'] = auth()->id();
        $inputs['updated_by'] = auth()->id();
        $dayItinerary = $this->dayItinerary->create($inputs);

        return response()->json([
            'success' => true,
            'message' => 'Day itinerary created successfully',
            'id' => $dayItinerary->id,
            'itineraryCount' => $travelRequest->travelRequestDayItineraries()->count(),
        ]);
    }

    /**
     * Update a single day itinerary entry (used from modal or AJAX)
     */
    public function update(UpdateRequest $request, $travelRequestId, $id)
    {
        $inputs = $request->validated();
        $dayItinerary = TravelRequestDayItinerary::findOrFail($id);
        $travelRequest = $dayItinerary->travelRequest;
        $inputs['updated_by'] = auth()->id();
        $updated = $dayItinerary->update($inputs);

        if ($updated) {
            return response()->json([
                'success' => true,
                'message' => 'Day itinerary updated successfully',
                'itineraryCount' => $travelRequest->travelRequestDayItineraries()->count(),
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Failed to update day itinerary',
        ], 422);
    }

    /**
     * Remove the specified day itinerary from storage.
     */
    public function destroy($travelRequestId, $id)
    {
        $dayItinerary = TravelRequestDayItinerary::findOrFail($id);
        $travelRequest = $dayItinerary->travelRequest;
        // $this->authorize('delete', $travelRequest);  

        $deleted = $dayItinerary->delete();
        if ($deleted) {
            return response()->json([
                'success' => true,
                'message' => 'Day itinerary deleted successfully',
                'itineraryCount' => $travelRequest->travelRequestDayItineraries()->count(),
            ]);
        }
        return response()->json([
            'success' => false,
            'message' => 'Failed to delete day itinerary',
        ], 422);
    }
}