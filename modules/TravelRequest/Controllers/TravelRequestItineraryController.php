<?php

namespace Modules\TravelRequest\Controllers;

use App\Http\Controllers\Controller;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Modules\Employee\Repositories\EmployeeRepository;
use Modules\Master\Repositories\AccountCodeRepository;
use Modules\Master\Repositories\ActivityCodeRepository;
use Modules\Master\Repositories\DonorCodeRepository;
use Modules\Master\Repositories\DsaCategoryRepository;
use Modules\Master\Repositories\OfficeRepository;
use Modules\Master\Repositories\TravelModeRepository;
use Modules\Privilege\Repositories\RoleRepository;
use Modules\Privilege\Repositories\UserRepository;
use Modules\TravelRequest\Repositories\TravelRequestItineraryRepository;
use Modules\TravelRequest\Repositories\TravelRequestRepository;
use Modules\TravelRequest\Requests\TravelRequestItinerary\StoreRequest;
use Modules\TravelRequest\Requests\TravelRequestItinerary\UpdateRequest;

class TravelRequestItineraryController extends Controller
{
    protected $destinationPath;

    /**
     * Create a new controller instance.
     *
     * @param  DsaCategoryRepository  $dsaCategory  ,
     * @param  EmployeeRepository  $employees  ,
     * @param  TravelRequestRepository  $travelRequest  ,
     * @param  TravelModeRepository  $travelModes  ,
     * @param  RoleRepository  $roles  ,
     */
    public function __construct(
        protected AccountCodeRepository $accountCodes,
        protected ActivityCodeRepository $activityCodes,
        protected DonorCodeRepository $donorCodes,
        protected DsaCategoryRepository $dsaCategory,
        protected EmployeeRepository $employees,
        protected TravelModeRepository $travelModes,
        protected TravelRequestRepository $travelRequest,
        protected TravelRequestItineraryRepository $travelRequestItinerary,
        protected RoleRepository $roles,
        protected UserRepository $user,
        protected OfficeRepository $offices
    ) {

        $this->destinationPath = 'travelRequest';
    }

    /**
     * Display a listing of the travel request itineraries
     *
     * @return mixed
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request, $travelRequestId)
    {
        if ($request->ajax()) {
            $authUser = auth()->user();
            $travelRequest = $this->travelRequest->find($travelRequestId);
            $data = $travelRequest->travelRequestItineraries()
                ->with(['dsaCategory', 'travelModes', 'activityCode']);

            $datatable = DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('activity', function ($row) {
                    return $row->activityCode->getActivityCode();
                })->addColumn('arrival_date', function ($row) {
                    return $row->getArrivalDate();
                })->addColumn('departure_date', function ($row) {
                    return $row->getDepartureDate();
                })->addColumn('mode_of_travel', function ($row) {
                    return $row->getTravelModes();
                })->addColumn('dsa_category', function ($row) {
                    return $row->getDsaCategory();
                })->addColumn('description', function ($row) {
                    return $row->description;
                })->addColumn('account', function ($row) {
                    return $row->accountCode->description;
                })->addColumn('donor', function ($row) {
                    return $row->donorCode->description;
                });

            if ($authUser->can('update', $travelRequest)) {
                $datatable->addColumn('action', function ($row) {
                    $btn = '<a data-toggle="modal" class="btn btn-outline-primary btn-sm open-itinerary-modal-form" href="';
                    $btn .= route('travel.requests.itinerary.edit', [$row->travel_request_id, $row->id]).'" rel="tooltip" title="Edit Travel Itinerary"><i class="bi-pencil-square"></i></a>';
                    $btn .= '&emsp;<a href = "javascript:;" class="btn btn-danger btn-sm delete-record" ';
                    $btn .= 'data-href="'.route('travel.requests.itinerary.destroy', [$row->travel_request_id, $row->id]).'" rel="tooltip" title="Delete Travel Itinerary">';
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
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function create($travelRequestId)
    {
        $travelRequest = $this->travelRequest->find($travelRequestId);
        $activityCodes = $this->activityCodes->getActiveActivityCodes();
        $donorCodes = $this->donorCodes->getActiveDonorCodes();
        $this->authorize('update', $travelRequest);

        return view('TravelRequest::TravelRequestItinerary.create', [
            'offices' => ($this->offices->getActiveOffices()),
            'activityCodes' => ($activityCodes),
            'donorCodes' => ($donorCodes),
            'dsaCategories' => ($this->dsaCategory->get()),
            'travelModes' => ($this->travelModes->get()),
            'travelRequest' => ($travelRequest),
        ]);
    }

    /**
     * Store a newly created travel request in storage.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(StoreRequest $request, $travelRequestId)
    {
        $inputs = $request->validated();
        $travelRequest = $this->travelRequest->find($travelRequestId);
        $this->authorize('update', $travelRequest);

        $inputs['travel_request_id'] = $travelRequest->id;
        $inputs['created_by'] = auth()->id();
        $travelRequestItinerary = $this->travelRequestItinerary->create($inputs);
        if ($travelRequestItinerary) {
            return response()->json(['status' => 'ok',
                'travelRequestItinerary' => $travelRequestItinerary,
                'itineraryCount' => $travelRequestItinerary->travelRequest->travelRequestItineraries()->count(),
                'message' => 'Travel Request Itinerary is successfully added.'], 200);
        }

        return response()->json(['status' => 'error',
            'message' => 'Travel Request Itinerary can not be added.'], 422);
    }

    /**
     * Show the form for editing the specified travel request.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function edit($travelRequestId, $id)
    {
        $travelRequestItinerary = $this->travelRequestItinerary->find($id);
        $this->authorize('update', $travelRequestItinerary->travelRequest);
        $accountCodes = $travelRequestItinerary->activityCode ? $travelRequestItinerary->activityCode->accountCodes()
            ->whereNotNull('activated_at')->orderBy('title', 'asc')->get() : collect();
        $activityCodes = $this->activityCodes->getActiveActivityCodes();
        $donorCodes = $this->donorCodes->getActiveDonorCodes();

        return view('TravelRequest::TravelRequestItinerary.edit')
            ->withOffices($this->offices->getActiveOffices())
            ->withAccountCodes($accountCodes)
            ->withActivityCodes($activityCodes)
            ->withDonorCodes($donorCodes)
            ->withDsaCategories($this->dsaCategory->get())
            ->withTravelModes($this->travelModes->get())
            ->withTravelRequestItinerary($travelRequestItinerary);
    }

    /**
     * Update the specified itinerary in storage.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(UpdateRequest $request, $travelRequestId, $id)
    {
        $travelRequestItinerary = $this->travelRequestItinerary->find($id);
        $this->authorize('update', $travelRequestItinerary->travelRequest);
        $inputs = $request->validated();
        $inputs['updated_by'] = auth()->id();
        $travelRequestItinerary = $this->travelRequestItinerary->update($id, $inputs);
        if ($travelRequestItinerary) {
            return response()->json(['status' => 'ok',
                'travelRequestItinerary' => $travelRequestItinerary,
                'itineraryCount' => $travelRequestItinerary->travelRequest->travelRequestItineraries()->count(),
                'message' => 'Travel Request Itinerary is successfully updated.'], 200);
        }

        return response()->json(['status' => 'error',
            'message' => 'Travel Request Itinerary can not be updated.'], 422);
    }

    /**
     * Remove the specified itinerary from storage.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy($travelRequestId, $id)
    {
        $travelRequest = $this->travelRequest->find($travelRequestId);
        $travelRequestItinerary = $this->travelRequestItinerary->find($id);
        $this->authorize('delete', $travelRequestItinerary->travelRequest);
        $flag = $this->travelRequestItinerary->destroy($id);
        if ($flag) {
            return response()->json([
                'type' => 'success',
                'itineraryCount' => $travelRequest->travelRequestItineraries()->count(),
                'message' => 'Travel Request Itinerary is successfully deleted.',
            ], 200);
        }

        return response()->json([
            'type' => 'error',
            'message' => 'Travel Request Itinerary can not deleted.',
        ], 422);
    }
}
