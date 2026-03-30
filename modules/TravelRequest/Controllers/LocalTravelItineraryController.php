<?php

namespace Modules\TravelRequest\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Storage;
use Modules\Master\Repositories\ActivityCodeRepository;
use Modules\Master\Repositories\DonorCodeRepository;
use Modules\TravelRequest\Repositories\LocalTravelRepository;
use Modules\TravelRequest\Repositories\LocalTravelItineraryRepository;
use Modules\Master\Repositories\TravelModeRepository;

use Modules\TravelRequest\Requests\LocalTravelItinerary\StoreRequest;
use Modules\TravelRequest\Requests\LocalTravelItinerary\UpdateRequest;

use DataTables;

class LocalTravelItineraryController extends Controller
{
    protected $destinationPath;
    /**
     * Create a new controller instance.
     *
     * @param ActivityCodeRepository $activityCodes
     * @param DonorCodeRepository $donorCodes
     * @param LocalTravelRepository $localTravels
     * @param LocalTravelItineraryRepository $localTravelItineraries
     * @param TravelModeRepository $travelModes
     */
    public function __construct(
        ActivityCodeRepository $activityCodes,
        DonorCodeRepository $donorCodes,
        LocalTravelRepository $localTravels,
        LocalTravelItineraryRepository $localTravelItineraries,
        TravelModeRepository $travelModes
    ) {
        $this->activityCodes = $activityCodes;
        $this->donorCodes = $donorCodes;
        $this->localTravels = $localTravels;
        $this->localTravelItineraries = $localTravelItineraries;
        $this->travelModes = $travelModes;
        $this->destinationPath = 'localTravel';
    }

    /**
     * Display a listing of the local travel itineraries
     *
     * @return mixed
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request, $localTravelId)
    {
        if ($request->ajax()) {
            $authUser = auth()->user();
            $localTravel = $this->localTravels->find($localTravelId);
            $data = $this->localTravelItineraries->select(['*'])
                ->where('local_travel_reimbursement_id', $localTravelId);
            $datatable = DataTables::of($data)
                ->addIndexColumn();
            if ($authUser->can('update', $localTravel)) {
                $datatable->addColumn('action', function ($row) use ($authUser, $localTravel) {
                    $btn = '<a data-toggle="modal" class="btn btn-outline-primary btn-sm open-itinerary-modal-form" href="';
                    $btn .= route('local.travel.reimbursements.itineraries.edit', [$row->local_travel_reimbursement_id, $row->id]) . '"><i class="bi-pencil-square"></i></a>';
                    $btn .= '&emsp;<a href = "javascript:;" class="btn btn-danger btn-sm delete-record" ';
                    $btn .= 'data-href="' . route('local.travel.reimbursements.itineraries.destroy', [$row->local_travel_reimbursement_id, $row->id]) . '">';
                    $btn .= '<i class="bi-trash"></i></a>';
                    return $btn;
                });
            }
            return $datatable->addColumn('travel_date', function ($row) {
                return $row->getTravelDate();
            })->addColumn('travel_mode', function ($row) {
                return $row->travel_mode;
            })->addColumn('attachment', function ($row) {
                $attachment = '';
                if (file_exists('storage/' . $row->attachment) && $row->attachment != '') {
                    $attachment = '<a href = "' . asset('storage/' . $row->attachment) . '" target = "_blank" class="fs-5" ';
                    $attachment .= 'title = "View Attachment" ><i class="bi bi-file-earmark-medical"></i></a>';
                }
                return $attachment;
            })->rawColumns(['action', 'attachment'])
            ->make(true);
        }
        return true;
    }

    /**
     * Show the form for creating a new local travel itinerary.
     *
     * @param $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function create($id)
    {
        $localTravel = $this->localTravels->find($id);
        $this->authorize('update', $localTravel);
        $authUser = auth()->user();
        $travelModes = $this->travelModes->select(['id', 'title'])->get();
        $activityCodes = $this->activityCodes->getActiveActivityCodes();
        $donorCodes = $this->donorCodes->getActiveDonorCodes();

        return view('TravelRequest::LocalTravel.Itinerary.create', [
            'activityCodes' => $activityCodes,
            'donorCodes' => $donorCodes,
            'localTravel' => $localTravel,
            'travelModes' => $travelModes,
        ]);
    }

    /**
     * Store a newly created local travel itinerary in storage.
     *
     * @param StoreRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreRequest $request, $id)
    {
        $localTravel = $this->localTravels->find($id);
        $this->authorize('update', $localTravel);
        $inputs = $request->validated();
        $inputs['number_of_travelers'] = $localTravel->number_of_travelers ?: 0;
        if ($request->file('attachment')) {
            $filename = $request->file('attachment')
                ->storeAs($this->destinationPath . '/' . $localTravel->id, time() . '_reimbursement_itinerary.' . $request->file('attachment')->getClientOriginalExtension());
            $inputs['attachment'] = $filename;
        }
        $inputs['local_travel_reimbursement_id'] = $localTravel->id;
        $inputs['created_by'] = auth()->id();
        $localTravelItinerary = $this->localTravelItineraries->create($inputs);

        if ($localTravelItinerary) {
            return response()->json([
                'status' => 'ok',
                'localTravelItinerary' => $localTravelItinerary,
                'message' => 'Local travel detail is successfully added.'
            ], 200);
        }
        return response()->json([
            'status' => 'error',
            'message' => 'Local travel detail can not be added.'
        ], 422);
    }

    /**
     * Show the form for editing the specified local travel itinerary.
     *
     * @param $prId
     * @param $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function edit($prId, $id)
    {
        $authUser = auth()->user();
        $localTravel = $this->localTravels->find($prId);
        $localTravelItinerary = $this->localTravelItineraries->find($id);
        $this->authorize('update', $localTravel);

        $travelModes = $this->travelModes->select(['id', 'title'])->get();
        $accountCodes = $localTravelItinerary->activityCode ? $localTravelItinerary->activityCode->accountCodes()
            ->whereNotNull('activated_at')->orderBy('title', 'asc')->get() : collect();
        $activityCodes = $this->activityCodes->getActiveActivityCodes();
        $donorCodes = $this->donorCodes->getActiveDonorCodes();

        return view('TravelRequest::LocalTravel.Itinerary.edit', [
            'accountCodes' => $accountCodes,
            'activityCodes' => $activityCodes,
            'donorCodes' => $donorCodes,
            'localTravelItinerary' => $localTravelItinerary,
            'travelModes' => $travelModes,
        ]);
    }

    /**
     * Update the specified local travel itinerary in storage.
     *
     * @param UpdateRequest $request
     * @param $prId
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(UpdateRequest $request, $prId, $id)
    {
        $localTravel = $this->localTravels->find($prId);
        $localTravelItinerary = $this->localTravelItineraries->find($id);
        $this->authorize('update', $localTravel);
        $inputs = $request->validated();
        if ($request->file('attachment')) {
            $filename = $request->file('attachment')
                ->storeAs($this->destinationPath . '/' . $localTravel->id, time() . '_reimbursement_itinerary.' . $request->file('attachment')->getClientOriginalExtension());
            $inputs['attachment'] = $filename;
        }
        $inputs['updated_by'] = auth()->id();
        $localTravelItinerary = $this->localTravelItineraries->update($id, $inputs);
        if ($localTravelItinerary) {
            return response()->json([
                'status' => 'ok',
                'purchaseRequestItem' => $localTravelItinerary,
                'message' => 'Local travel detail is successfully updated.'
            ], 200);
        }
        return response()->json([
            'status' => 'error',
            'message' => 'Local travel detail can not be updated.'
        ], 422);
    }

    /**
     * Remove the specified local travel itinerary from storage.
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy($prId, $id)
    {
        $localTravel = $this->localTravels->find($prId);
        $localTravelItinerary = $this->localTravelItineraries->find($id);
        $this->authorize('update', $localTravel);
        $flag = $this->localTravelItineraries->destroy($id);
        if ($flag) {
            return response()->json([
                'type' => 'success',
                'message' => 'Local travel detail is successfully deleted.',
            ], 200);
        }
        return response()->json([
            'type' => 'error',
            'message' => 'Local travel detail can not deleted.',
        ], 422);
    }
}
