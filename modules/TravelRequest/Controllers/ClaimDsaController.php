<?php

namespace Modules\TravelRequest\Controllers;

use DataTables;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Modules\Master\Repositories\OfficeRepository;
use Modules\Master\Repositories\TravelModeRepository;
use Modules\TravelRequest\Repositories\TravelClaimRepository;
use Modules\TravelRequest\Requests\Claim\DsaClaim\StoreRequest;
use Modules\TravelRequest\Repositories\TravelClaimDsaRepository;
use Modules\TravelRequest\Requests\Claim\DsaClaim\UpdateRequest;

class ClaimDsaController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param TravelClaimRepository $travelClaims
     * @param TravelClaimDsaRepository $travelDsaClaims
     * @param TravelModeRepository $travelModes
     */
    public function __construct(
        protected TravelClaimRepository $travelClaims,
        protected TravelClaimDsaRepository $travelDsaClaims,
        protected TravelModeRepository $travelModes,
        protected OfficeRepository $offices
    ) {
        $this->travelClaims = $travelClaims;
        $this->travelDsaClaims = $travelDsaClaims;
        $this->offices = $offices;
        $this->destinationPath = 'travelRequest';
    }

    /**
     * Display a listing of the travel claim expenses
     *
     * @return mixed
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request, $travelClaimId)
    {
        if ($request->ajax()) {
            $authUser = auth()->user();
            $travelClaim = $this->travelClaims->find($travelClaimId);
            $data = $this->travelDsaClaims->select(['*'])->with(['activityCode', 'donorCode', 'office'])
                ->whereTravelClaimId($travelClaimId)->get();
            $datatable = DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('charging_office', function ($row) {
                    return $row->office->getOfficeName();
                })
                ->addColumn('expense_date', function ($row) {
                    return $row->getExpenseDate();
                })->addColumn('attachment', function ($row) {
                    $attachment = '';
                    if (file_exists('storage/' . $row->attachment) && $row->attachment != '') {
                        $attachment = '<a href = "' . asset('storage/' . $row->attachment) . '" target = "_blank" class="fs-5" ';
                        $attachment .= 'title = "View Attachment" ><i class="bi bi-file-earmark-medical"></i></a>';
                    }
                    return $attachment;
                })->addColumn('action', function ($row) use ($authUser, $travelClaim) {
                    $btn = '';
                    if ($authUser->can('update', $travelClaim)) {
                        $btn = '<a data-toggle="modal" class="btn btn-outline-primary btn-sm open-itinerary-modal-form" href="';
                        $btn .= route('travel.claims.dsa.edit', [$row->travel_claim_id, $row->id]) . '"><i class="bi-pencil-square"></i></a>';
                        $btn .= '&emsp;<a href = "javascript:;" class="btn btn-danger btn-sm delete-record" ';
                        $btn .= 'data-href="' . route('travel.claims.dsa.destroy', [$row->travel_claim_id, $row->id]) . '">';
                        $btn .= '<i class="bi-trash"></i></a>';
                    }
                    return $btn;
                });
            return $datatable->addColumn('activity', function ($row) {
                return $row->activityCode?->getActivityCodeDescription();
            })->addColumn('donor', function ($row) {
                return $row->donorCode->description;
            })->rawColumns(['action', 'attachment'])
                ->make(true);
        }
        return true;
    }

    /**
     * Show the form for creating a new travel claim expense.
     *
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function create($id)
    {
        $travelClaim = $this->travelClaims->find($id);
        $offices = $this->offices->getActiveOffices();

        return view('TravelRequest::TravelClaim.DsaClaim.create')
            ->withTravelModes($this->travelModes->get())
            ->withTravelClaim($travelClaim);
    }

    /**
     * Store a newly created travel claim expense in storage.
     *
     * @param StoreRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreRequest $request, $id)
    {
        $authUser = auth()->user();
        $travelClaim = $this->travelClaims->find($id);
        $inputs = $request->validated();
        if ($request->file('attachment')) {
            $filename = $request->file('attachment')
                ->storeAs($this->destinationPath . '/' . $travelClaim->travel_request_id, time() . '_claim_dsa.' . $request->file('attachment')->getClientOriginalExtension());
            $inputs['attachment'] = $filename;
        }
        $inputs['travel_claim_id'] = $travelClaim->id;
        $inputs['created_by'] = $authUser->id;
        $travelDsaClaim = $this->travelDsaClaims->create($inputs);

        if ($travelDsaClaim) {
            return response()->json([
                'status' => 'ok',
                'travelClaim' => $travelDsaClaim->travelClaim,
                'travelDsaClaim' => $travelDsaClaim,
                'message' => 'Travel Request Claim Itinerary is successfully added.'
            ], 200);
        }
        return response()->json([
            'status' => 'error',
            'message' => 'Travel Request Claim Itinerary can not be added.'
        ], 422);
    }

    /**
     * Show the form for editing the specified travel claim expense.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function edit($claimId, $id)
    {
        $travelClaim = $this->travelClaims->find($claimId);
        $travelClaimExpense = $this->travelDsaClaims->find($id);
        $this->authorize('update', $travelClaim);
        $offices = $this->offices->getActiveOffices();

        return view('TravelRequest::TravelClaim.DsaClaim.edit')
            ->withTravelModes($this->travelModes->get())
            ->withTravelClaim($travelClaim);
    }

    /**
     * Update the specified travel claim expense in storage.
     *
     * @param UpdateRequest $request
     * @param $claimId
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(UpdateRequest $request, $claimId, $id)
    {
        $travelClaimExpense = $this->travelDsaClaims->find($id);
        $this->authorize('update', $travelClaimExpense->travelClaim);
        $inputs = $request->validated();
        if ($request->file('attachment')) {
            $filename = $request->file('attachment')
                ->storeAs($this->destinationPath . '/' . $travelClaimExpense->travelClaim->travel_request_id, time() . '_claim_expense.' . $request->file('attachment')->getClientOriginalExtension());
            $inputs['attachment'] = $filename;
        }
        $inputs['updated_by'] = auth()->id();
        $travelClaimExpense = $this->travelDsaClaims->update($id, $inputs);
        if ($travelClaimExpense) {
            return response()->json([
                'status' => 'ok',
                'travelClaim' => $travelClaimExpense->travelClaim,
                'travelClaimExpense' => $travelClaimExpense,
                'message' => 'Travel expense is successfully updated.'
            ], 200);
        }
        return response()->json([
            'status' => 'error',
            'message' => 'Travel expense can not be updated.'
        ], 422);
    }

    /**
     * Remove the specified travel claim expense from storage.
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy($claimId, $id)
    {
        $travelClaimExpense = $this->travelDsaClaims->find($id);
        $this->authorize('delete', $travelClaimExpense->travelClaim);
        $flag = $this->travelDsaClaims->destroy($id);
        if ($flag) {
            $travelClaim = $this->travelClaims->find($claimId);
            return response()->json([
                'type' => 'success',
                'travelClaim' => $travelClaim,
                'message' => 'Travel expense is successfully deleted.',
            ], 200);
        }
        return response()->json([
            'type' => 'error',
            'travelClaim' => $travelClaimExpense->travelClaim,
            'message' => 'Travel expense can not deleted.',
        ], 422);
    }
}
