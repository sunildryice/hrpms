<?php

namespace Modules\Mfr\Controllers;

use App\Http\Controllers\Controller;
use Modules\Mfr\Notifications\TransactionSubmitted;
use Modules\Mfr\Repositories\AgreementRepository;
use Modules\Mfr\Repositories\TransactionRepository;
use Modules\Mfr\Requests\Transaction\StoreRequest;
use Modules\Mfr\Requests\Transaction\UpdateRequest;
use Modules\Privilege\Repositories\UserRepository;

class TransactionController extends Controller
{
    public function __construct(
        protected TransactionRepository $transactions,
        protected AgreementRepository $agreements,
        protected UserRepository $users
    ) {}

    public function index()
    {
        return view('Mfr::Transaction.index');
    }

    public function show($transactionId)
    {
        return view('Mfr::Transaction.show', ['transaction' => $this->transactions->find($transactionId)]);
    }

    public function create($agreementId)
    {
        $agreement = $this->agreements->find($agreementId);

        return view('Mfr::Transaction.create', ['agreement' => $agreement]);
    }

    public function store(StoreRequest $request, $agreementId)
    {
        $inputs = $request->validated();
        $agreement = $this->agreements->find($agreementId);

        if ($agreement->transactions()->whereNot('status_id', config('constant.APPROVED_STATUS'))->first()) {
            return redirect()->back()->withErrorMessage('Incomplete transactions exists');
        }

        // $this->authorize('createTransaction', $agreement);
        $inputs['mfr_agreement_id'] = $agreement->id;
        $inputs['approved_budget'] = $agreement->getApprovedBudget();

        //removed due to client requeest
        // $dupTransaction = $this->transactions->where('mfr_agreement_id', '=', $agreement->id)
        //     ->where('transaction_type', '=', $inputs['transaction_type'])
        //     ->where('transaction_date', '=', $inputs['transaction_date'])
        //     ->first();
        // if ($dupTransaction) {
        //     return redirect()->back()->withErrorMessage('Transaction of given nature and date already exists');
        // }
        // dd($inputs, $agreement->toArray(), $inputs['transaction_date'] > $agreement->start_date);

        //check date
        if ($inputs['transaction_date'] < $agreement->getEffectiveFromDate() || $inputs['transaction_date'] > $agreement->getEffectiveToDate()) {
            return redirect()->back()->withErrorMessage('Transaction date should be within agreement date');
        }

        $transaction = $this->transactions->create($inputs);
        if ($transaction) {
            return redirect()->route('mfr.transaction.edit', [$transaction->id])->withSuccessMessage('Transaction created successfully');
        }

        return redirect()->back()->withErrorMessage('Transaction Could not be created');
    }

    public function edit($transactionId)
    {
        $authUser = auth()->user();
        $transaction = $this->transactions->find($transactionId);
        $this->authorize('update', $transaction);
        $agreement = $transaction->agreement;

        return view('Mfr::Transaction.edit', [
            'transaction' => $transaction,
            'agreement' => $agreement,
            'reviewers' => $this->users->getSupervisors($authUser),
        ]);
    }

    public function update(UpdateRequest $request, $transactionId)
    {
        $transaction = $this->transactions->with('agreement')->find($transactionId);
        $this->authorize('update', $transaction);
        $inputs = $request->validated();
        $inputs['approved_budget'] = $transaction->agreement->getApprovedBudget();

        // check transaction amount against approved budget
        $sum = $this->transactions->select('release_amount')
            ->where('mfr_agreement_id', $transaction->mfr_agreement_id)
            // ->whereDate('transaction_date', '<>', $transaction->transaction_date)
            ->where('id', '<>', $transaction->id)
            ->sum('release_amount')
            + $inputs['release_amount'];

        if ($sum > $transaction->agreement->getApprovedBudget()) {
            return redirect()->back()->withInput()->withErrorMessage('Transaction amount exceeds approved budget');
        }

        //check date
        if ($inputs['transaction_date'] < $transaction->agreement->getEffectiveFromDate() || $inputs['transaction_date'] > $transaction->agreement->getEffectiveToDate()) {
            return redirect()->back()->withErrorMessage('Transaction date should be within agreement date');
        }

        $transaction = $this->transactions->update($transaction->id, $inputs);
        if ($transaction) {
            $message = 'Transaction Updated Successfully';
            if ($transaction->status_id == config('constant.SUBMITTED_STATUS')) {
                $transaction->reviewer->notify(new TransactionSubmitted($transaction));
                // foreach ($this->users->permissionBasedUsers('review-mfr-transaction') as $user) {
                //     $user->notify(new TransactionSubmitted($transaction));
                // }
                $message = 'Transaction Submitted Successfully';

                return redirect()->route('mfr.agreement.show.transactions', $transaction->agreement->id)->withSuccessMessage($message);
            }

            return redirect()->back()->withInput()->withSuccessMessage($message);
        }
    }

    public function destroy($transactionId)
    {
        $transaction = $this->transactions->find($transactionId);
        $this->authorize('delete', $transaction);
        $flag = $this->transactions->destroy($transactionId);
        if ($flag) {
            return response()->json(['message' => 'Transaction Deleted Successfully'], 200);
        }

        return response()->json(['message' => 'Transaction Could not be deleted'], 422);
    }

    public function print($transactionId)
    {
        $transaction = $this->transactions->find($transactionId);
        $this->authorize('print', $transaction);

        return view('Mfr::Transaction.print', ['transaction' => $transaction, 'agreement' => $transaction->agreement]);
    }
}
