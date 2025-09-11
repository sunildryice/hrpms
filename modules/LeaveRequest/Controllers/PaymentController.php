<?php

namespace Modules\LeaveRequest\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\LeaveRequest\Notifications\LeaveEncashPaid;
use Modules\LeaveRequest\Repositories\LeaveEncashRepository;
use Modules\LeaveRequest\Requests\Payment\StoreRequest;
use Yajra\DataTables\DataTables;

class PaymentController extends Controller
{

    private $leaveEncash;

    public function __construct(
        LeaveEncashRepository $leaveEncash,

    ) {
        $this->leaveEncash = $leaveEncash;

    }

    public function index(Request $request)
    {

        $authUser = auth()->user();
        if ($request->ajax()) {
            $data = $this->leaveEncash->getPaid();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('leave_type', function ($row) {
                    return $row->getLeaveType();
                })->addColumn('request_date', function ($row) {
                return $row->getRequestDate();
            })->addColumn('encash_balance', function ($row) {
                return $row->encash_balance;
            })->addColumn('encash_number', function ($row) {
                return $row->getEncashNumber();
            })->addColumn('requester', function ($row) {
                return $row->getRequesterName();
            })->addColumn('status', function ($row) {
                return '<span class="' . $row->getStatusClass() . '">' . $row->getStatus() . '</span>';
            })->addColumn('action', function ($row) use ($authUser) {
                $btn = '<a class="btn btn-outline-primary btn-sm" href="';
                $btn .= route('paid.leave.encash.show', $row->id) . '" rel="tooltip" title="View  Payment Sheet">';
                $btn .= '<i class="bi bi-eye"></i></a>';
                $btn .= '&emsp;<a class="btn btn-outline-primary btn-sm" href="';
                $btn .= route('leave.encash.print', $row->id) . '" target="_blank" rel="tooltip" title="Print Payment Sheet">';
                $btn .= '<i class="bi bi-printer"></i></a>';
                return $btn;
            })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }
        return view('LeaveRequest::LeaveEncash.Paid.index');
    }

    public function show($Id)
    {
        $leaveEncash = $this->leaveEncash->find($Id);
        return view('LeaveRequest::LeaveEncash.Paid.show')
            ->withLeaveEncash($leaveEncash);
    }

    public function create($id)
    {
        $leaveEncash = $this->leaveEncash->find($id);
        $this->authorize('pay', $leaveEncash);
        return view('LeaveRequest::Payment.create')
            ->withLeaveEncash($leaveEncash);
    }

    public function store(StoreRequest $request, $id)
    {
        $leaveEncash = $this->leaveEncash->find($id);
        $this->authorize('pay', $leaveEncash);
        $inputs = $request->validated();
        $inputs['user_id'] = auth()->id();
        $inputs['original_user_id'] = session()->has('original_user') ? session()->get('original_user') : null;
        $inputs['paid_at'] = date('Y-m-d H:i:s');
        $leaveEncash = $this->leaveEncash->pay($id, $inputs);
        if ($leaveEncash) {
            $leaveEncash->requester->notify(new LeaveEncashPaid($leaveEncash));
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

}
