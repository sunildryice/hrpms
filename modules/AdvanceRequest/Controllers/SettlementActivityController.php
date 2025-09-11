<?php

namespace Modules\AdvanceRequest\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Modules\Employee\Repositories\EmployeeRepository;
use Modules\AdvanceRequest\Notifications\AdvanceRequestSubmitted;
use Modules\Master\Repositories\FiscalYearRepository;
use Modules\Privilege\Repositories\UserRepository;

use Modules\AdvanceRequest\Requests\SettlementActivity\StoreRequest;
use Modules\AdvanceRequest\Requests\SettlementActivity\UpdateRequest;

use Modules\AdvanceRequest\Repositories\AdvanceRequestRepository;
use Modules\AdvanceRequest\Repositories\AdvanceRequestDetailsRepository;
use Modules\AdvanceRequest\Repositories\SettlementActivityRepository;
use Modules\AdvanceRequest\Repositories\SettlementRepository;
use Modules\Master\Repositories\DistrictRepository;
use Modules\Master\Repositories\ProjectCodeRepository;
use Modules\Master\Repositories\AccountCodeRepository;
use Modules\Master\Repositories\ActivityCodeRepository;
use Modules\Master\Repositories\DonorCodeRepository;
use DataTables;

class SettlementActivityController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @param DistrictRepository $districts
     * @param EmployeeRepository $employees
     * @param FiscalYearRepository $fiscalYears
     * @param AdvanceRequestRepository $advanceRequests
     * @param AdvanceRequestDetailsRepository $advanceRequestsDetails
     * @param ProjectCodeRepository $projects
     * @param UserRepository $users
     * @param AccountCodeRepository $accountCodes
     * @param ActivityCodeRepository $activityCodes
     * @param SettlementActivityRepository $settlementActivity
     * @param SettlementRepository $settlements
     * @param DonorCodeRepository $donorCodes
     */
    public function __construct(
        DistrictRepository              $districts,
        EmployeeRepository              $employees,
        FiscalYearRepository            $fiscalYears,
        AdvanceRequestRepository        $advanceRequests,
        AdvanceRequestDetailsRepository $advanceRequestsDetails,
        ProjectCodeRepository           $projects,
        UserRepository                  $users,
        AccountCodeRepository           $accountCodes,
        ActivityCodeRepository          $activityCodes,
        SettlementActivityRepository    $settlementActivity,
        SettlementRepository            $settlements,
        DonorCodeRepository             $donorCodes
    )
    {

        $this->accountCodes = $accountCodes;
        $this->activityCodes = $activityCodes;
        $this->donorCodes = $donorCodes;
        $this->districts = $districts;
        $this->projects = $projects;
        $this->employees = $employees;
        $this->fiscalYears = $fiscalYears;
        $this->advanceRequests = $advanceRequests;
        $this->advanceRequestsDetails = $advanceRequestsDetails;
        $this->settlementActivity = $settlementActivity;
        $this->settlements = $settlements;
        $this->users = $users;
        $this->destinationPath = 'advanceRequest';
    }

    /**
     * Display a listing of the advance requests
     *
     * @return mixed
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request, $advanceSettlementId)
    {
        if ($request->ajax()) {
            $authUser = auth()->user();
            $advanceSettle = $this->settlements->find($advanceSettlementId);
            $data = $this->settlementActivity->select([
                'id', 'advance_settlement_id', 'description'])->where('advance_settlement_id', '=', $advanceSettlementId);
            $datatable = Datatables::of($data)
                ->addIndexColumn();
            if ($authUser->can('update', $advanceSettle)) {
                $datatable->addColumn('action', function ($row) use ($authUser, $advanceSettle) {
                    $btn = '<a data-toggle="modal" class="btn btn-outline-primary btn-sm open-activity-modal-form" href="';
                    $btn .= route('advance.settlement.activities.edit', [$row->advance_settlement_id, $row->id]) . '"><i class="bi-pencil"></i></a>';
                    $btn .= '&emsp;<a href = "javascript:;" class="btn btn-danger btn-sm delete-record" ';
                    $btn .= 'data-href="' . route('advance.settlement.activities.destroy', [$row->advance_settlement_id, $row->id]) . '">';
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
     * Show the form for creating a new advance settlement request.
     *
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function create($id)
    {
        $authUser = auth()->user();
        $settlements = $this->settlements->find($id);
        return view('AdvanceRequest::Settlement.Activity.create')
            ->withSettlements($settlements);
    }

    /**
     * Store a newly created advance settlement request in storage.
     *
     * @param StoreRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreRequest $request, $id)
    {
        $authUser = auth()->user();
        $settlementRequest = $this->settlements->find($id);
        // $this->authorize('create-advance-request');
        $inputs = $request->validated();
        $inputs['advance_settlement_id'] = $settlementRequest->id;
        $settlementActivity = $this->settlementActivity->create($inputs);
        if ($settlementActivity) {
            return response()->json(['status' => 'ok',
                'settlementActivity' => $settlementActivity,
                'message' => 'Settlement Activity is successfully added.'], 200);
        }
        return response()->json(['status' => 'error',
            'message' => 'Settlement Activity  Detail can not be added.'], 422);
    }


    /**
     * Show the form for editing the specified advance settlement request.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function edit($prId, $id)
    {
        $authUser = auth()->user();
        // $advanceRequest = $this->advanceRequests->find($prId);
        $settlementActivity = $this->settlementActivity->find($id);
        // $this->authorize('update', $advanceRequest);

        return view('AdvanceRequest::Settlement.Activity.edit')
            ->withSettlementActivity($settlementActivity);
    }


    /**
     * Update the specified advance settlement request in storage.
     *
     * @param UpdateRequest $request
     * @param $prId
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateRequest $request, $prId, $id)
    {

        $settlementActivity = $this->settlementActivity->find($id);
        // $this->authorize('update', $settlementActivity->advanceRequest);
        $inputs = $request->validated();
        $settlementActivity = $this->settlementActivity->update($id, $inputs);
        if ($settlementActivity) {
            return response()->json(['status' => 'ok',
                'settlementActivity' => $settlementActivity,
                'message' => 'SettlementActivity  is successfully updated.'], 200);
        }
        return response()->json(['status' => 'error',
            'message' => 'SettlementActivity  can not be updated.'], 422);
    }


    /**
     * Remove the specified Settlement Activity from storage.
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy($prId, $id)
    {
        $settlementActivity = $this->settlementActivity->find($id);
        // $this->authorize('delete', $settlementActivity->advanceRequest);
        $flag = $this->settlementActivity->destroy($id);
        if ($flag) {
            return response()->json([
                'type' => 'success',
                'message' => 'Settlement Activity is successfully deleted.',
            ], 200);
        }
        return response()->json([
            'type' => 'error',
            'message' => 'Settlement Activity can not deleted.',
        ], 422);
    }


}
