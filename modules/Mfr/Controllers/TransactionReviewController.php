<?php

namespace Modules\Mfr\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Mfr\Notifications\TransactionReturned;
use Modules\Mfr\Notifications\TransactionReviewed;
use Modules\Mfr\Repositories\TransactionRepository;
use Modules\Mfr\Requests\Transaction\Review\StoreRequest;
use Modules\Privilege\Repositories\UserRepository;
use Yajra\DataTables\DataTables;

class TransactionReviewController extends Controller
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
            $data = $this->transactions->where('status_id', '=', config('constant.SUBMITTED_STATUS'))
                ->where('reviewer_id', $authUser->id)
                ->with('agreement.partnerOrganization')
                ->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('transaction_date', function ($row) {
                    return $row->transaction_date->format('Y-m-d');
                })
                ->addColumn('partner_organization', function ($row) {
                    return $row->getPOName();
                })
                ->addColumn('status', function ($row) {
                    return '<span class="'.$row->getStatusClass().'">'.$row->getStatus().'</span>';
                })
                ->addColumn('action', function ($row) use ($authUser) {
                    $btn = '';
                    if ($authUser->can('review', $row)) {
                        $btn .= '<a class="btn btn-outline-primary btn-sm" href="';
                        $btn .= route('mfr.transaction.review.create', $row->id).'" rel="tooltip" title="Review Transaction"><i class="bi bi-pencil-square"></i></a>';
                    }

                    return $btn;

                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }

        return view('Mfr::Transaction.Review.index');
    }

    public function create($transactionId)
    {
        $transaction = $this->transactions->find($transactionId);

        $this->authorize('review', $transaction);

        return view('Mfr::Transaction.Review.create', ['transaction' => $transaction,
            'agreement' => $transaction->agreement,
            'verifiers' => $this->users->permissionBasedUsers('verify-mfr-transaction')]);
    }

    public function store(StoreRequest $request, $transactionId)
    {
        $transaction = $this->transactions->find($transactionId);

        $this->authorize('review', $transaction);

        $inputs = $request->validated();
        $inputs['user_id'] = $inputs['reviewer_id'] = auth()->id();
        $inputs['original_user_id'] = session()->has('original_user') ? session()->get('original_user') : null;
        $transaction = $this->transactions->verify($transactionId, $inputs);

        if ($transaction) {
            $message = '';
            if ($transaction->status_id == config('constant.RETURNED_STATUS')) {
                $message = 'Transaction is successfully returned.';
                $transaction->requester->notify(new TransactionReturned($transaction));
            } else {
                $message = 'Transaction is successfully reviewed.';
                $transaction->verifier->notify(new TransactionReviewed($transaction));
                // foreach ($this->users->permissionBasedUsers('approve-mfr-transaction') as $user) {
                //     $user->notify(new TransactionReviewed($transaction));
                // }
            }

            return redirect()->route('mfr.transaction.review.index')->withSuccessMessage($message);
        }

        return redirect()->back()->withInput()->withWarningMessage('Transaction cannot be verified.');
    }

    public function view($id)
    {

    }
}
