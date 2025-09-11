<?php

namespace Modules\AdvanceRequest\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Modules\AdvanceRequest\Notifications\AdvanceRequestVerified;
use Modules\AdvanceRequest\Notifications\AdvanceRequestReturned;
use Modules\AdvanceRequest\Repositories\AdvanceRequestRepository;
use Modules\Privilege\Repositories\UserRepository;
use Modules\AdvanceRequest\Requests\Verify\StoreRequest;
use DataTables;


class VerifyController extends Controller
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
                ->where(function ($q) use ($authUser) {
                    $q->where('verifier_id', $authUser->id);
                    $q->where('status_id', config('constant.SUBMITTED_STATUS'));
                });

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
                })->addColumn('action', function ($row) use ($authUser) {
                    $btn = '<a class="btn btn-outline-primary btn-sm" href="';
                    $btn .= route('verify.advance.requests.create', $row->id) . '" rel="tooltip" title="Verify Advance Request">';
                    $btn .= '<i class="bi bi-box-arrow-in-up-right"></i></a>';
                    return $btn;
                })->addColumn('attachment', function ($row) use ($authUser) {
                    $attachment = '';
                    if($row->attachment){
                        $attachment .= '<div class="media"><a href="'.asset('storage/'.$row->attachment).'" target="_blank" class="fs-5" title="View Attachment">';
                        $attachment.= '<i class="bi bi-file-earmark-medical"></i></a></div>';
                    }
                    return $attachment;
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }

        return view('AdvanceRequest::Verify.index');
    }

    public function create($advanceRequestId)
    {
        $authUser = auth()->user();
        $advanceRequest = $this->advanceRequests->find($advanceRequestId);
        $this->authorize('verify', $advanceRequest);

        return view('AdvanceRequest::Verify.create')
            ->withAuthUser($authUser)
            ->withAdvanceRequest($advanceRequest);
    }

    public function store(StoreRequest $request, $advanceRequestId)
    {
        $advanceRequest = $this->advanceRequests->find($advanceRequestId);
        $inputs = $request->validated();
        $inputs['user_id'] = auth()->id();
        $inputs['original_user_id'] = session()->has('original_user') ? session()->get('original_user') : null;
        $advanceRequest = $this->advanceRequests->verify($advanceRequest->id, $inputs);
        if ($advanceRequest) {
            $message = '';
            if ($advanceRequest->status_id == config('constant.RETURNED_STATUS')) {
                $message = 'Advance request is successfully returned.';
                 $advanceRequest->requester->notify(new AdvanceRequestReturned($advanceRequest));
            } else {
                $message = 'Advance request is successfully verified.';
                $advanceRequest->approver->notify(new AdvanceRequestVerified($advanceRequest));
            }
            return redirect()->route('verify.advance.requests.index')
                ->withSuccessMessage($message);
        }
        return redirect()->back()
            ->withInput()
            ->withWarningMessage('Advance request can not be verified.');
    }
}
