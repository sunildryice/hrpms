<?php

namespace Modules\TravelAuthorization\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Storage;

use Modules\Master\Repositories\AccountCodeRepository;
use Modules\Master\Repositories\ActivityCodeRepository;
use Modules\Master\Repositories\DonorCodeRepository;
use Modules\Master\Repositories\DsaCategoryRepository;
use Modules\Employee\Repositories\EmployeeRepository;
use Modules\TravelAuthorization\Repositories\TravelAuthorizationItineraryRepository;
use Modules\TravelAuthorization\Repositories\TravelAuthorizationRepository;
use Modules\Master\Repositories\TravelModeRepository;
use Modules\Privilege\Repositories\RoleRepository;
use Modules\Privilege\Repositories\UserRepository;

use Modules\TravelAuthorization\Requests\Itinerary\StoreRequest;
use Modules\TravelAuthorization\Requests\Itinerary\UpdateRequest;

use DB;
use DataTables;

class TravelAuthorizationItineraryController extends Controller
{
    public function __construct(
        protected AccountCodeRepository            $accountCodes,
        protected ActivityCodeRepository           $activityCodes,
        protected DonorCodeRepository              $donorCodes,
        protected DsaCategoryRepository          $dsaCategory,
        protected EmployeeRepository               $employees,
        protected TravelModeRepository            $travelModes,
        protected TravelAuthorizationRepository          $travel,
        protected TravelAuthorizationItineraryRepository $travelItinerary,
        protected RoleRepository                   $roles,
        protected UserRepository                   $user
    )
    {

        $this->destinationPath = 'travelAuthorization';
    }

    /**
     * Display a listing of the travel request itineraries
     *
     * @return mixed
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request, $taId)
    {
        if ($request->ajax()) {
            $authUser = auth()->user();
            $travel = $this->travel->find($taId);
            $data = $this->travelItinerary->select(['*'])
                ->where('travel_authorization_id', $taId);

            $datatable = DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('travel_date', function ($row) {
                    return $row->travel_date->format('Y-m-d');
                });
            if ($authUser->can('update', $travel)) {
                $datatable->addColumn('action', function ($row) use ($authUser, $travel) {
                    $btn = '<a data-toggle="modal" class="btn btn-outline-primary btn-sm open-itinerary-modal-form" href="';
                    $btn .= route('ta.requests.itinerary.edit', [$row->travel_authorization_id, $row->id]) . '" rel="tooltip" title="Edit Travel Itinerary"><i class="bi-pencil-square"></i></a>';
                    $btn .= '&emsp;<a href = "javascript:;" class="btn btn-danger btn-sm delete-record" ';
                    $btn .= 'data-href="' . route('ta.requests.itinerary.destroy', [$row->travel_authorization_id, $row->id]) . '" rel="tooltip" title="Delete Travel Itinerary">';
                    $btn .= '<i class="bi-trash"></i></a>';
                    return $btn;
                });
            }
            return $datatable->rawColumns(['action'])
                ->make(true);
        }
        return true;
    }

    /**
     * Show the form for creating a new travel request itinerary.
     *
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function create($taId)
    {
        $travel = $this->travel->find($taId);
        $this->authorize('update', $travel);
        return view('TravelAuthorization::Itinerary.create')
            ->withTravel($travel);
    }

    /**
     * Store a newly created travel request in storage.
     *
     * @param StoreRequest $request
     * @param $taId
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(StoreRequest $request, $taId)
    {
        $inputs = $request->validated();
        $travel = $this->travel->find($taId);
        $this->authorize('update', $travel);
        $inputs['travel_authorization_id'] = $travel->id;
        $inputs['created_by'] = auth()->id();
        $travelItinerary = $this->travelItinerary->create($inputs);
        if ($travelItinerary) {
            return response()->json(['status' => 'ok',
                'itineraryCount'=>$travelItinerary->travelAuthorization->itineraries()->count(),
                'message' => 'Travel Itinerary is successfully added.'], 200);
        }
        return response()->json(['status' => 'error',
            'message' => 'Travel Itinerary can not be added.'], 422);
    }

    /**
     * Show the form for editing the specified travel request.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function edit($taId, $id)
    {
        $travelItinerary = $this->travelItinerary->find($id);
        $this->authorize('update', $travelItinerary->travelAuthorization);
        return view('TravelAuthorization::Itinerary.edit')
            ->withItinerary($travelItinerary);
    }

    /**
     * Update the specified itinerary in storage.
     *
     * @param UpdateRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(UpdateRequest $request, $taId, $id)
    {
        $travelItinerary = $this->travelItinerary->find($id);
        $this->authorize('update', $travelItinerary->travelAuthorization);
        $inputs = $request->validated();
        $inputs['updated_by'] = auth()->id();
        $travelItinerary = $this->travelItinerary->update($id, $inputs);
        if ($travelItinerary) {
            return response()->json(['status' => 'ok',
                'travelItinerary' => $travelItinerary,
                'itineraryCount'=>$travelItinerary->travelAuthorization->itineraries()->count(),
                'message' => 'Travel Itinerary  is successfully updated.'], 200);
        }
        return response()->json(['status' => 'error',
            'message' => 'Travel Itinerary  can not be updated.'], 422);
    }

    /**
     * Remove the specified itinerary from storage.
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy($taId, $id)
    {
        $travel = $this->travel->find($taId);
        $travelItinerary = $this->travelItinerary->find($id);
        $this->authorize('delete', $travelItinerary->travelAuthorization);
        $flag = $this->travelItinerary->destroy($id);
        if ($flag) {
            return response()->json([
                'type' => 'success',
                'itineraryCount'=>$travel->itineraries()->count(),
                'message' => 'Travel Itinerary is successfully deleted.',
            ], 200);
        }
        return response()->json([
            'type' => 'error',
            'message' => 'Travel Itinerary can not deleted.',
        ], 422);
    }
}
