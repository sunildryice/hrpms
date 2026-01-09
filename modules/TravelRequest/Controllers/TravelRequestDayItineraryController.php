<?php

namespace Modules\TravelRequest\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;
use Modules\TravelRequest\Models\TravelRequest;
use Modules\TravelRequest\Models\TravelRequestDayItinerary;
use Modules\TravelRequest\Repositories\TravelRequestRepository;
use Modules\TravelRequest\Repositories\TravelRequestDayItineraryRepository;

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
        if ($request->ajax()) {
            $travelRequest = $this->travelRequest->findOrFail($travelRequestId);

            $data = $travelRequest->dayItineraries();

            $datatable = DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('date', fn($row) => $row->date?->format('d M Y'))
                ->addColumn('planned_activities', fn($row) => $row->planned_activities ?: '<em class="text-muted">No activities</em>')
                ->addColumn('accommodation', fn($row) => $row->accommodation
                    ? '<i class="bi bi-check-lg text-success"></i>'
                    : '<i class="bi bi-x-lg text-muted"></i>')
                ->addColumn('air_ticket', function ($row) {
                    if (!$row->air_ticket) {
                        return '<i class="bi bi-x-lg text-muted"></i>';
                    }

                    $places = '';
                    if ($row->departure_place && $row->arrival_place) {
                        $places = " ({$row->departure_place} to {$row->arrival_place})";
                    } elseif ($row->departure_place) {
                        $places = " ({$row->departure_place})";
                    } elseif ($row->arrival_place) {
                        $places = " (to {$row->arrival_place})";
                    }

                    $time = $row->departure_time ? " {$row->departure_time}" : '';

                    return '<i class="bi bi-check-lg text-success"></i>' . $places . $time;
                })
                ->addColumn('action', function ($row) use ($travelRequestId) {
                    $btn = '<a class="btn btn-outline-primary btn-sm open-day-itinerary-modal-form" 
                               href="' . route('travel.requests.day-itinerary.edit', [$travelRequestId, $row->id]) . '" 
                               title="Edit Day Itinerary">
                               <i class="bi bi-pencil-square"></i>
                           </a>';

                    $btn .= ' <a href="javascript:;" class="btn btn-danger btn-sm delete-record" 
                                 data-href="' . route('travel.requests.day-itinerary.destroy', [$travelRequestId, $row->id]) . '" 
                                 title="Delete Day Itinerary">
                                 <i class="bi bi-trash"></i>
                             </a>';

                    return $btn;
                })
                ->rawColumns(['planned_activities', 'accommodation', 'air_ticket', 'action']);

            return $datatable->make(true);
        }

        abort(403);
    }

    /**
     * Update a single day itinerary entry (used from modal or AJAX)
     */
    public function update(Request $request, $travelRequestId, $id)
    {
        $dayItinerary = TravelRequestDayItinerary::findOrFail($id);
        $travelRequest = $dayItinerary->travelRequest;

        $this->authorize('update', $travelRequest);

        $validated = $request->validate([
            'planned_activities' => 'nullable|string|max:2000',
            'accommodation'      => 'boolean',
            'air_ticket'         => 'boolean',
            'departure_place'    => 'nullable|string|max:255|required_if:air_ticket,true',
            'arrival_place'      => 'nullable|string|max:255|required_if:air_ticket,true',
            'departure_time'     => 'nullable|string|max:50',
        ]);

        $validated['updated_by'] = auth()->id();

        $updated = $dayItinerary->update($validated);

        if ($updated) {
            return response()->json([
                'success' => true,
                'message' => 'Day itinerary updated successfully',
                'dayCount' => $travelRequest->dayItineraries()->count(),
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Failed to update day itinerary',
        ], 422);
    }

    /**
     * Delete a single day itinerary entry
     */
    public function destroy($travelRequestId, $id)
    {
        $dayItinerary = TravelRequestDayItinerary::findOrFail($id);
        $travelRequest = $dayItinerary->travelRequest;

        $this->authorize('delete', $travelRequest);

        $deleted = $dayItinerary->delete();

        if ($deleted) {
            return response()->json([
                'success' => true,
                'message' => 'Day itinerary deleted successfully',
                'dayCount' => $travelRequest->dayItineraries()->count(),
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Failed to delete day itinerary',
        ], 422);
    }
}