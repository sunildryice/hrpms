<?php

namespace Modules\AdvanceRequest\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\AdvanceRequest\Notifications\AdvancePaid;
use Modules\AdvanceRequest\Repositories\AdvanceRequestRepository;
use Modules\AdvanceRequest\Requests\Payment\StoreRequest;
use Yajra\DataTables\DataTables;

class AdvancePaymentController extends Controller
{
    public function __construct(
        protected AdvanceRequestRepository $advances
    ) {
    }

    public function index(Request $request)
    {
        $authUser = auth()->user();
        if ($request->ajax()) {
            $data = $this->advances->getPaid();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('advance_number', function ($row) {
                    return $row->getAdvanceRequestNumber();
                })->addColumn('project_code', function ($row) {
                    return $row->getProjectCode();
                }) ->addColumn('requester', function ($row) {
                    return $row->requester->getFullName();
                }) ->addColumn('district', function ($row) {
                    return $row->district->getDistrictName();
                }) ->addColumn('office', function ($row) {
                    return $row->office->getOfficeName();
                }) ->addColumn('required_date', function ($row) {
                    return $row->getRequiredDate();
                })->addColumn('estimated_amount', function ($row) {
                    return $row->getEstimatedAmount();
                })->addColumn('status', function ($row) {
                    return '<span class="'.$row->getStatusClass().'">'.$row->getStatus().'</span>';
                })->addColumn('action', function ($row) use ($authUser) {
                    $btn = '<a class="btn btn-outline-primary btn-sm" href="';
                    $btn .= route('paid.advance.show', $row->id) . '" rel="tooltip" title="View  Advance Request">';
                    $btn .= '<i class="bi bi-eye"></i></a>';
                    $btn .= '&emsp;<a class="btn btn-outline-primary btn-sm" href="';
                    $btn .= route('advance.request.print', $row->id) . '" target="_blank" rel="tooltip" title="Print Advance Request">';
                    $btn .= '<i class="bi bi-printer"></i></a>';

                    if($authUser->can('close', $row)){
                        $btn .= '&emsp;<button class="btn btn-danger btn-sm close-advance-modal-form" href="';
                        $btn .= route('close.advance.requests.create', $row->id) . '" rel="tooltip" title="Close ADV"><i class="bi bi-x-circle"></i></button>';
                    }
                    return $btn;
                })->addColumn('attachment', function ($row) {
                    $attachment = '';
                    if ($row->attachment) {
                        $attachment .= '<div class="media"><a href="'.asset('storage/'.$row->attachment).'" target="_blank" class="fs-5" title="View Attachment">';
                        $attachment .= '<i class="bi bi-file-earmark-medical"></i></a></div>';
                    }

                    return $attachment;
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }

        return view('AdvanceRequest::Paid.index');
    }

    public function create($id)
    {
        $advance = $this->advances->find($id);
        $this->authorize('pay', $advance);

        return view('AdvanceRequest::Payment.Advance.create')
            ->withAdvance($advance);
    }

    public function store(StoreRequest $request, $id)
    {
        $inputs = $request->validated();
        $advance = $this->advances->find($id);
        $this->authorize('pay', $advance);
        $inputs['user_id'] = auth()->id();
        $inputs['original_user_id'] = session()->has('original_user') ? session()->get('original_user') : null;
        $inputs['paid_at'] = date('Y-m-d H:i:s');
        $advance = $this->advances->pay($id, $inputs);
        if ($advance) {
            $advance->requester->notify(new AdvancePaid($advance));

            return response()->json([
                'status' => 'ok',
                'message' => 'Payment is successfully made.',
            ], 200);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Payment can not be made.',
        ], 422);
    }

    public function show($advanceRequestId)
    {
        $advanceRequest = $this->advances->find($advanceRequestId);
        $this->authorize('viewApproved', $advanceRequest);
        return view('AdvanceRequest::Paid.show')
            ->withAdvanceRequest($advanceRequest);
    }
}
