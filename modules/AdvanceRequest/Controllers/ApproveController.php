<?php

namespace Modules\AdvanceRequest\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Modules\AdvanceRequest\Notifications\AdvanceRequestApproved;
use Modules\AdvanceRequest\Notifications\AdvanceRequestRejected;
use Modules\AdvanceRequest\Notifications\AdvanceRequestReturned;
use Modules\AdvanceRequest\Notifications\AdvanceRequestRecommended;
use Modules\AdvanceRequest\Repositories\AdvanceRequestRepository;
use Modules\Privilege\Repositories\UserRepository;
use Modules\AdvanceRequest\Requests\Approve\StoreRequest;
use DataTables;


class ApproveController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param AdvanceRequestRepository $advanceRequests
     * @param UserRepository $users
     */
    public function __construct(
        AdvanceRequestRepository $advanceRequests,
        UserRepository            $users
    )
    {
        $this->advanceRequests = $advanceRequests;
        $this->users = $users;
    }

    /**
     * Display a listing of the advance requests
     *
     * @return mixed
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request)
    {
        $authUser = auth()->user();
        if ($request->ajax()) {
            $data = $this->advanceRequests->with(['fiscalYear', 'status'])->select(['*'])
                ->where('approver_id', $authUser->id)
                ->whereIn('status_id', [config('constant.VERIFIED_STATUS'), config('constant.RECOMMENDED_STATUS')]);

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('advance_number', function ($row) {
                    return $row->getAdvanceRequestNumber();
                })->addColumn('project_code', function ($row){
                    return $row->getProjectCode();
                })->addColumn('required_date', function ($row){
                    return $row->getRequiredDate();
                })->addColumn('estimated_amount', function ($row) {
                    return $row->getEstimatedAmount();
                })->addColumn('status', function ($row){
                    return '<span class="'.$row->getStatusClass() .'">'.$row->getStatus().'</span>';
                })->addColumn('attachment', function ($row) use ($authUser) {
                    $attachment = '';
                    if($row->attachment){
                        $attachment .= '<div class="media"><a href="'.asset('storage/'.$row->attachment).'" target="_blank" class="fs-5" title="View Attachment">';
                        $attachment.= '<i class="bi bi-file-earmark-medical"></i></a></div>';
                    }
                    return $attachment;
                })
                ->addColumn('action', function ($row) use ($authUser) {
                    $btn = '<a class="btn btn-outline-primary btn-sm" href="';
                    $btn .= route('approve.advance.requests.create', $row->id) . '" rel="tooltip" title="Approve Advance Request">';
                    $btn .= '<i class="bi bi-box-arrow-in-up-right"></i></a>';
                    return $btn;
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }

        return view('AdvanceRequest::Approve.index');
    }

    public function create($advanceRequestId)
    {
        $authUser = auth()->user();
        $advanceRequest = $this->advanceRequests->find($advanceRequestId);
        $this->authorize('approve', $advanceRequest);

        $supervisors = $this->users->permissionBasedUsers('approve-advance-request');
        $supervisors = $supervisors->reject(function($supervisor) use ($advanceRequest){
            return $advanceRequest->requester_id == $supervisor->id;
        });

        return view('AdvanceRequest::Approve.create')
            ->withAuthUser($authUser)
            ->withAdvanceRequest($advanceRequest)
            ->withSupervisors($supervisors);
    }

    public function store(StoreRequest $request, $advanceRequestId)
    {
        $advanceRequest = $this->advanceRequests->find($advanceRequestId);
        $inputs = $request->validated();
        $inputs['user_id'] = auth()->id();
        $inputs['original_user_id'] = session()->has('original_user') ? session()->get('original_user') : null;
        $advanceRequest = $this->advanceRequests->approve($advanceRequest->id, $inputs);
        if ($advanceRequest) {
            $message = '';
            if ($advanceRequest->status_id == config('constant.RETURNED_STATUS')) {
                $message = 'Advance request is successfully returned.';
                 $advanceRequest->requester->notify(new AdvanceRequestReturned($advanceRequest));
            } else if ($advanceRequest->status_id == config('constant.REJECTED_STATUS')) {
                $message = 'Advance request is successfully rejected.';
                 $advanceRequest->requester->notify(new AdvanceRequestRejected($advanceRequest));
            } else if ($advanceRequest->status_id == config('constant.RECOMMENDED_STATUS')) {
                $message = 'Advance request is successfully recommended.';
                 $advanceRequest->approver->notify(new AdvanceRequestRecommended($advanceRequest));
            } else {
                $message = 'Advance request is successfully approved.';
                 $advanceRequest->requester->notify(new AdvanceRequestApproved($advanceRequest));
            }
            return redirect()->route('approve.advance.requests.index')
                ->withSuccessMessage($message);
        }

        return redirect()->back()
            ->withInput()
            ->withWarningMessage('Advance request can not be approved.');
    }
}
