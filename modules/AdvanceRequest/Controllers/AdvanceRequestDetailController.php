<?php

namespace Modules\AdvanceRequest\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Modules\AdvanceRequest\Requests\Detail\StoreRequest;
use Modules\AdvanceRequest\Requests\Detail\UpdateRequest;
use Modules\AdvanceRequest\Repositories\AdvanceRequestRepository;
use Modules\AdvanceRequest\Repositories\AdvanceRequestDetailsRepository;
use Modules\Master\Repositories\AccountCodeRepository;
use Modules\Master\Repositories\ActivityCodeRepository;
use Modules\Master\Repositories\DonorCodeRepository;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class AdvanceRequestDetailController extends Controller
{
    private $advanceRequestDetails;
    private $advanceRequests;
    private $accountCodes;
    private $activityCodes;
    private $donorCodes;
    private $destinationPath;

    /**
     * Create a new controller instance.
     *
     * @param AdvanceRequestRepository $advanceRequests
     * @param AdvanceRequestDetailsRepository $advanceRequestDetails
     * @param AccountCodeRepository $accountCodes
     * @param ActivityCodeRepository $activityCodes
     * @param DonorCodeRepository $donorCodes
     */
    public function __construct(
        AdvanceRequestRepository        $advanceRequests,
        AdvanceRequestDetailsRepository $advanceRequestDetails,
        AccountCodeRepository           $accountCodes,
        ActivityCodeRepository          $activityCodes,
        DonorCodeRepository             $donorCodes
    )
    {

        $this->advanceRequests = $advanceRequests;
        $this->advanceRequestDetails = $advanceRequestDetails;
        $this->accountCodes = $accountCodes;
        $this->activityCodes = $activityCodes;
        $this->donorCodes = $donorCodes;
        $this->destinationPath = 'advanceRequest';
    }

    /**
     * Display a listing of the advance requests details
     *
     * @return mixed
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request, $advanceRequestId)
    {
        if ($request->ajax()) {
            $authUser = auth()->user();
            $advanceRequest = $this->advanceRequests->find($advanceRequestId);
            $data = $this->advanceRequestDetails->select([
                'id', 'advance_request_id', 'activity_code_id', 'account_code_id', 'activity_code_id', 'donor_code_id', 'description', 'amount', 'attachment'])
                ->whereAdvanceRequestId($advanceRequestId);
            $datatable = DataTables::of($data)
                ->addIndexColumn();
            if ($authUser->can('update', $advanceRequest)) {
                $datatable->addColumn('action', function ($row) use ($authUser, $advanceRequest) {
                    $btn = '<a data-toggle="modal" class="btn btn-outline-primary btn-sm open-advance-detail-modal-form" href="';
                    $btn .= route('advance.requests.details.edit', [$row->advance_request_id, $row->id]) . '"><i class="bi-pencil-square"></i></a>';
                    $btn .= '&emsp;<a href = "javascript:;" class="btn btn-danger btn-sm delete-record" ';
                    $btn .= 'data-href="' . route('advance.requests.details.destroy', [$row->advance_request_id, $row->id]) . '">';
                    $btn .= '<i class="bi-trash"></i></a>';
                    return $btn;
                });
            }

            return $datatable->addColumn('activity', function ($row) {
                return $row->getActivityCode();
            })->addColumn('account', function ($row) {
                return $row->getAccountCode();
            })->addColumn('donor', function ($row) {
                return $row->donor_code_id?$row->getDonorCode():'';
            })->addColumn('attachment', function ($row) use ($authUser) {
                $attachment = '';
                if($row->attachment){
                    $attachment .= '<div class="media"><a href="'.asset('storage/'.$row->attachment).'" target="_blank" class="fs-5" title="View Attachment">';
                    $attachment.= '<i class="bi bi-file-earmark-medical"></i></a></div>';
                }
                return $attachment;
            })->rawColumns(['action','attachment'])
                ->make(true);
        }
        return true;
    }

    /**
     * Show the form for creating a new advance request detail by employee.
     *
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function create($id)
    {
        $authUser = auth()->user();
        $advanceRequest = $this->advanceRequests->find($id);
        $this->authorize('update', $advanceRequest);
        $activityCodes = $this->activityCodes->getActiveActivityCodes();
        $donorCodes = $this->donorCodes->getActiveDonorCodes();
        $advanceRequest = $this->advanceRequests->find($id);
        return view('AdvanceRequest::Detail.create')
            ->withActivityCodes($activityCodes)
            ->withDonorCodes($donorCodes)
            ->withAdvanceRequest($advanceRequest);
    }

    /**
     * Store a newly created advance request detail in storage.
     *
     * @param StoreRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(StoreRequest $request, $id)
    {
        $authUser = auth()->user();
        $advanceRequest = $this->advanceRequests->find($id);
        $this->authorize('update', $advanceRequest);
        $inputs = $request->validated();
        $inputs['advance_request_id'] = $advanceRequest->id;
        $advanceRequestDetail = $this->advanceRequestDetails->create($inputs);
        if($advanceRequestDetail){
            if ($request->file('attachment')) {
                $filename = $request->file('attachment')
                    ->storeAs($this->destinationPath .'/'.$authUser->employee_id, time().'_advance.'. $request->file('attachment')->getClientOriginalExtension());
                $inputs['attachment'] = $filename;
                $advanceRequestDetail = $this->advanceRequestDetails->update($advanceRequestDetail->id, $inputs);
            }

            return response()->json(['status' => 'ok',
                'advanceRequestDetail' => $advanceRequestDetail,
                'advanceDetailCount'=>$advanceRequest->advanceRequestDetails()->count(),
                'message' => 'Advance Request Detail is successfully added.'], 200);
        }
        return response()->json(['status' => 'error',
            'message' => 'Advance Request Detail can not be added.'], 422);

    }


    /**
     * Show the form for editing the specified advance request detail.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function edit($advanceId, $id)
    {
        $authUser = auth()->user();
        $advanceRequest = $this->advanceRequests->find($advanceId);
        $advanceRequestDetails = $this->advanceRequestDetails->find($id);
        $this->authorize('update', $advanceRequest);

        $accountCodes = $advanceRequestDetails->activityCode ? $advanceRequestDetails->activityCode->accountCodes()
            ->whereNotNull('activated_at')->orderBy('title', 'asc')->get() : collect();
        $activityCodes = $this->activityCodes->getActiveActivityCodes();
        $donorCodes = $this->donorCodes->getActiveDonorCodes();

        return view('AdvanceRequest::Detail.edit')
            ->withAccountCodes($accountCodes)
            ->withActivityCodes($activityCodes)
            ->withDonorCodes($donorCodes)
            ->withAdvanceRequestDetails($advanceRequestDetails);
    }


    /**
     * Update the specified advance request detail in storage.
     *
     * @param UpdateRequest $request
     * @param $advanceId
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(UpdateRequest $request, $advanceId, $id)
    {
        $authUser = auth()->user();
        $advanceRequestDetails = $this->advanceRequestDetails->find($id);
        $this->authorize('update', $advanceRequestDetails->advanceRequest);
        $inputs = $request->validated();
        $inputs['attachment'] = $advanceRequestDetails->attachment;
        if ($request->file('attachment')) {
            $filename = $request->file('attachment')
                ->storeAs($this->destinationPath .'/'.$authUser->employee_id, time().'_advance.'. $request->file('attachment')->getClientOriginalExtension());
            $inputs['attachment'] = $filename;
        }
        $advanceRequestDetails = $this->advanceRequestDetails->update($id, $inputs);
        if ($advanceRequestDetails) {
            $advanceRequest = $this->advanceRequests->find($advanceId);
            return response()->json(['status' => 'ok',
                'advanceRequestDetails' => $advanceRequestDetails,
                'advanceDetailCount'=>$advanceRequest->advanceRequestDetails()->count(),
                'message' => 'Advance request detail is successfully updated.'], 200);
        }
        return response()->json(['status' => 'error',
            'message' => 'Advance request detail can not be updated.'], 422);
    }


    /**
     * Remove the specified Advance Request Detail from storage.
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy($advanceId, $id)
    {
        $advanceRequestsDetail = $this->advanceRequestDetails->find($id);
        $this->authorize('delete', $advanceRequestsDetail->advanceRequest);
        $flag = $this->advanceRequestDetails->destroy($id);
        if ($flag) {
            $advanceRequest = $this->advanceRequests->find($advanceId);
            return response()->json([
                'type' => 'success',
                'advanceDetailCount'=>$advanceRequest->advanceRequestDetails()->count(),
                'message' => 'Advance request detail is successfully deleted.',
            ], 200);
        }
        return response()->json([
            'type' => 'error',
            'message' => 'Advance request detail can not deleted.',
        ], 422);
    }

    
    public function deleteAttachment($advanceRequestDetail)
    {
        $advanceRequestDetail = $this->advanceRequestDetails->find($advanceRequestDetail);
        $this->authorize('delete', $advanceRequestDetail->advanceRequest);
        DB::beginTransaction();
        try {
            $advanceRequestDetail->attachment = null;
            $advanceRequestDetail->save();
            DB::commit();
            return response()->json([
                'type' => 'success',
                'message' => 'Attachment deleted successfully.'
            ], 200);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json([
                'type' => 'error',
                'message' => 'Attachment could not be successfully.'
            ], 422);
        }
        
    }


}
