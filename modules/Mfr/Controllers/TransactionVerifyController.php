<?php

namespace Modules\Mfr\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Mfr\Notifications\TransactionRecommended;
use Modules\Mfr\Notifications\TransactionReturned;
use Modules\Mfr\Notifications\TransactionVerified;
use Modules\Mfr\Notifications\TransactionVerifyd;
use Modules\Mfr\Repositories\TransactionRepository;
use Modules\Mfr\Requests\Transaction\Verify\StoreRequest;
use Modules\Privilege\Repositories\UserRepository;
use Yajra\DataTables\DataTables;

class TransactionVerifyController extends Controller
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
            $data = $this->transactions->where('status_id', '=', config('constant.VERIFIED_STATUS'))
                ->where('verifier_id', $authUser->id)
                ->with('agreement.partnerOrganization')
                ->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('transaction_date', function ($row) {
                    return $row->transaction_date->format('Y-m-d');
                })
                ->addColumn('status', function ($row) {
                    return '<span class="'.$row->getStatusClass().'">'.$row->getStatus().'</span>';
                })
                ->addColumn('partner_organization', function ($row) {
                    return $row->getPOName();
                })
                ->addColumn('action', function ($row) use ($authUser) {
                    if ($authUser->can('verify', $row)) {
                        $btn = '<a class="btn btn-outline-primary btn-sm" href="';
                        $btn .= route('mfr.transaction.verify.create', $row->id).'"  rel="tooltip" title="Verify Transaction"><i class="bi bi-pencil-square"></i></a>';

                        return $btn;
                    }
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }

        return view('Mfr::Transaction.Verify.index');
    }

    public function create($transactionId)
    {
        $transaction = $this->transactions->find($transactionId);
        $recommenders = $this->users->permissionBasedUsers('recommend-mfr-transaction');

        $this->authorize('verify', $transaction);

        return view('Mfr::Transaction.Verify.create', ['transaction' => $transaction, 'agreement' => $transaction->agreement, 'recommenders' => $recommenders]);
    }

    public function store(StoreRequest $request, $transactionId)
    {
        $transaction = $this->transactions->find($transactionId);
        $this->authorize('verify', $transaction);
        $inputs = $request->validated();
        $inputs['user_id'] = auth()->id();
        $inputs['original_user_id'] = session()->has('original_user') ? session()->get('original_user') : null;
        $transaction = $this->transactions->verify($transaction->id, $inputs);

        if ($transaction) {

            $message = '';
            if ($transaction->status_id == config('constant.RETURNED_STATUS')) {
                $message = 'Transaction is successfully returned.';
                $transaction->requester->notify(new TransactionReturned($transaction));
            } elseif ($transaction->status_id == config('constant.VERIFIED2_STATUS')) {
                $message = 'Transaction is successfully verified.';
                $transaction->recommender->notify(new TransactionVerified($transaction));
            } else {
                $message = 'Transaction is successfully verified.';
            }

            return redirect()->route('mfr.transaction.verify.index')
                ->withSuccessMessage($message);
        }

        return redirect()->back()
            ->withInput()
            ->withWarningMessage('Transaction could not be verified');
    }
}
