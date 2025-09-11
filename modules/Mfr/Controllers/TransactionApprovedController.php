<?php

namespace Modules\Mfr\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Mfr\Notifications\TransactionApproved;
use Modules\Mfr\Notifications\TransactionRecommended;
use Modules\Mfr\Notifications\TransactionReturned;
use Modules\Mfr\Repositories\TransactionRepository;
use Modules\Mfr\Requests\Transaction\Approve\StoreRequest;
use Modules\Privilege\Repositories\UserRepository;
use Yajra\DataTables\DataTables;

class TransactionApprovedController extends Controller
{
    public function __construct(
        protected TransactionRepository $transactions,
        protected UserRepository $users
    ) {
    }

    public function index(Request $request)
    {
        $authUser = auth()->user();
        if ($request->ajax()) {
            $data = $this->transactions->where('status_id', '=', config('constant.APPROVED_STATUS'))
                ->with(['agreement.partnerOrganization', 'requester'])
                // ->where('requester_id', $authUser->id)
                // ->orWhere('approver_id', $authUser->id)
                // ->orWhere('recommender_id', $authUser->id)
                // ->orWhere('reviewer_id', $authUser->id)
                // ->orWhere('verifier_id', $authUser->id)
                ->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('partner_organization', function ($row) {
                    return $row->getPOName();
                })
                ->addColumn('transaction_date', function ($row) {
                    return $row->transaction_date->format('Y M d');
                })
                ->addColumn('questioned_cost', function ($row) {
                    return $row->getQuestionedCost();
                })
                ->addColumn('requester', function ($row) {
                    return $row->getRequester();
                })
                ->addColumn('action', function ($row) use ($authUser) {
                    $btn = '<a class="btn btn-outline-primary btn-sm" href="';
                    $btn .= route('mfr.transaction.approved.show', [$row->id]).'" rel="tooltip" title="View approved"><i class="bi bi-eye"></i></a>';

                    if ($authUser->can('print', $row)) {
                        $btn .= '&emsp;<a class="btn btn-outline-primary btn-sm" href="';
                        $btn .= route('mfr.transaction.print', $row->id).'" target="_blank" rel="tooltip" title="Print"><i class="bi bi-printer"></i></a>';
                    }                    return $btn;

                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }

        return view('Mfr::Transaction.Approved.index');
    }

    public function show($transactionId)
    {
        return view('Mfr::Transaction.Approved.show', ['transaction' => $this->transactions->find($transactionId)]);
    }

}
