<?php

namespace Modules\Mfr\Repositories;

use App\Repositories\Repository;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Modules\Mfr\Models\Agreement;
use Modules\Mfr\Models\Transaction;

class TransactionRepository extends Repository
{
    public function __construct(
        Transaction $transaction,
    ) {
        $this->model = $transaction;
    }

    public function create($inputs)
    {
        DB::beginTransaction();
        try {
            $inputs['status_id'] = config('constant.CREATED_STATUS');
            $inputs['requester_id'] = auth()->id();
            $transaction = $this->model->create($inputs);
            // dd($transaction);
            DB::commit();

            return $transaction;
        } catch (QueryException $e) {
            DB::rollBack();
            dd($e);

            return false;
        }
    }

    public function update($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $transaction = $this->model->findOrFail($id);
            $transaction->fill($inputs)->save();
            if ($inputs['btn'] == 'submit') {
                $forwardInputs = [
                    'user_id' => auth()->id(),
                    'log_remarks' => 'Transaction is Submitted',
                    $inputs['original_user_id'] = session()->has('original_user') ? session()->get('original_user') : null,
                ];
                $transaction = $this->forward($transaction->id, $forwardInputs);
            }
            DB::commit();

            return $transaction;
        } catch (QueryException $e) {
            DB::rollBack();

            return false;
        }
    }

    public function forward($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $transaction = $this->model->findOrFail($id);
            $inputs['status_id'] = config('constant.SUBMITTED_STATUS');

            $transaction->update($inputs);
            $transaction->logs()->create($inputs);
            DB::commit();

            return $transaction;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();

            return false;
        }
    }

    public function createOpening(Agreement $agreement)
    {
        if($agreement->opening_balance == 0) {
            return;
        }
        DB::beginTransaction();
        try {
            $transaction = $this->model->create([
                'mfr_agreement_id' => $agreement->id,
                'requester_id' => $agreement->created_by,
                'transaction_type' => '1',
                'release_amount' => $agreement->opening_balance,
                'remarks' => $agreement->opening_remarks,
                'transaction_date' => $agreement->effective_from,
                'status_id' => config('constant.APPROVED_STATUS'),
            ]);
            $transaction->logs()->create([
                'user_id' => $agreement->created_by,
                'log_remarks' => 'Opening balance created.',
                'status_id' => config('constant.APPROVED_STATUS'),
            ]);
            DB::commit();
            return $transaction;
        } catch (QueryException $e) {
            DB::rollBack();
            return false;
        }
    }

    public function verify($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $transaction = $this->model->find($id);
            $transaction->fill($inputs)->save();
            $transaction->logs()->create($inputs);
            DB::commit();

            return $transaction;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();

            return false;
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $transaction = $this->model->findOrFail($id);
            $transaction->logs()->delete();
            $transaction->delete();
            DB::commit();

            return $transaction;
        } catch (QueryException $e) {
            DB::rollBack();

            return false;
        }
    }
}
