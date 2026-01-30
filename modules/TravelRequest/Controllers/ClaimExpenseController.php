<?php

namespace Modules\TravelRequest\Controllers;

use DataTables;
use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Modules\Master\Repositories\OfficeRepository;
use Modules\Project\Repositories\ProjectRepository;
use Modules\Master\Repositories\DonorCodeRepository;
use Modules\Master\Repositories\ActivityCodeRepository;

use Modules\Project\Repositories\ProjectActivityRepository;
use Modules\TravelRequest\Repositories\TravelClaimRepository;

use Modules\TravelRequest\Requests\Claim\Expense\StoreRequest;
use Modules\TravelRequest\Requests\Claim\Expense\UpdateRequest;
use Modules\TravelRequest\Repositories\TravelClaimExpenseRepository;

class ClaimExpenseController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param ActivityCodeRepository $activityCodes
     * @param TravelClaimRepository $travelClaims
     * @param TravelClaimExpenseRepository $travelExpenses
     */
    public function __construct(
        ActivityCodeRepository $activityCodes,
        DonorCodeRepository $donorCodes,
        TravelClaimRepository $travelClaims,
        TravelClaimExpenseRepository $travelExpenses,
        OfficeRepository $offices,
        protected ProjectRepository $projects,
        protected ProjectActivityRepository $projectActivities,
    ) {
        $this->activityCodes = $activityCodes;
        $this->donorCodes = $donorCodes;
        $this->travelClaims = $travelClaims;
        $this->travelExpenses = $travelExpenses;
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
            $data = $this->travelExpenses->select(['*'])->with(['activity', 'donorCode', 'office'])
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
                        $btn = '<a data-toggle="modal" class="btn btn-outline-primary btn-sm open-expense-modal-form" href="';
                        $btn .= route('travel.claims.expenses.edit', [$row->travel_claim_id, $row->id]) . '"><i class="bi-pencil-square"></i></a>';
                        $btn .= '&emsp;<a href = "javascript:;" class="btn btn-danger btn-sm delete-record" ';
                        $btn .= 'data-href="' . route('travel.claims.expenses.destroy', [$row->travel_claim_id, $row->id]) . '">';
                        $btn .= '<i class="bi-trash"></i></a>';
                    }
                    return $btn;
                });
            return $datatable->addColumn('activity', function ($row) {
                return $row->activity?->title;
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
        $authUser = auth()->user();
        // $activityCodes = $this->activityCodes->getActiveActivityCodes();
        $travelClaim = $this->travelClaims->find($id);
        $projectId = $travelClaim?->travelRequest?->project_code_id;
        $activityCodes = $this->projectActivities->getActivitiesByProject($authUser);
        // ->where($projectId, 'project_id');
        $offices = $this->offices->getActiveOffices();

        return view('TravelRequest::TravelClaim.Expense.create')
            ->withOffices($offices)
            ->withActivityCodes($activityCodes)
            ->withDonorCodes($this->donorCodes->getActiveDonorCodes())
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
                ->storeAs($this->destinationPath . '/' . $travelClaim->travel_request_id, time() . '_claim_expense.' . $request->file('attachment')->getClientOriginalExtension());
            $inputs['attachment'] = $filename;
        }
        $inputs['travel_claim_id'] = $travelClaim->id;
        $inputs['created_by'] = $authUser->id;
        $travelClaimExpense = $this->travelExpenses->create($inputs);

        if ($travelClaimExpense) {
            return response()->json([
                'status' => 'ok',
                'travelClaim' => $travelClaimExpense->travelClaim,
                'travelClaimExpense' => $travelClaimExpense,
                'message' => 'Travel expense is successfully added.'
            ], 200);
        }
        return response()->json([
            'status' => 'error',
            'message' => 'Travel expense can not be added.'
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
        $authUser = auth()->user();
        $travelClaim = $this->travelClaims->find($claimId);
        $projectId = $travelClaim?->travelRequest?->project_code_id;
        $activityCodes = $this->projectActivities->getActivitiesByProject($authUser);
        // ->where($projectId, 'project_id');
        $travelClaimExpense = $this->travelExpenses->find($id);
        $this->authorize('update', $travelClaim);
        // $activityCodes = $this->activityCodes->getActiveActivityCodes();
        $offices = $this->offices->getActiveOffices();

        return view('TravelRequest::TravelClaim.Expense.edit')
            ->withOffices($offices)
            ->withActivityCodes($activityCodes)
            ->withDonorCodes($this->donorCodes->getActiveDonorCodes())
            ->withTravelExpense($travelClaimExpense);
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
        $travelClaimExpense = $this->travelExpenses->find($id);
        $this->authorize('update', $travelClaimExpense->travelClaim);
        $inputs = $request->validated();
        if ($request->file('attachment')) {
            $filename = $request->file('attachment')
                ->storeAs($this->destinationPath . '/' . $travelClaimExpense->travelClaim->travel_request_id, time() . '_claim_expense.' . $request->file('attachment')->getClientOriginalExtension());
            $inputs['attachment'] = $filename;
        }
        $inputs['updated_by'] = auth()->id();
        $travelClaimExpense = $this->travelExpenses->update($id, $inputs);
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
        $travelClaimExpense = $this->travelExpenses->find($id);
        $this->authorize('delete', $travelClaimExpense->travelClaim);
        $flag = $this->travelExpenses->destroy($id);
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
