<?php

namespace Modules\Mfr\Repositories;

use App\Repositories\Repository;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Modules\Mfr\Models\Agreement;

class AgreementRepository extends Repository
{
    public function __construct(
        Agreement $agreement,
        protected TransactionRepository $transactions,
    ) {
        $this->model = $agreement;
    }

    public function getApproved()
    {
        $authUser = auth()->user();
        $currentOffice = $authUser->getCurrentOffice();
        $accessibleOfficeIds = $authUser->getAccessibleOfficesIds();

        if ($currentOffice) {
            if ($currentOffice->office_type_id == config('constant.HEAD_OFFICE')) {
                return $this->model
                    ->whereIn('status_id', [config('constant.APPROVED_STATUS')])
                    ->whereIn('office_id', $accessibleOfficeIds)
                    ->orWhere(function ($q) {
                        $q->whereNull('office_id');
                        $q->whereIn('status_id', [config('constant.APPROVED_STATUS')]);
                    })
                    ->orderBy('created_at', 'desc')->get();
            }
        }

        return $this->model
            ->whereIn('status_id', [config('constant.APPROVED_STATUS')])
            ->whereIn('office_id', $accessibleOfficeIds)
            ->orderBy('created_at', 'desc')->get();
    }

    public function getPending()
    {
        $authUser = auth()->user();
        $currentOffice = $authUser->getCurrentOffice();
        $accessibleOfficeIds = $authUser->getAccessibleOfficesIds();

        if ($currentOffice) {
            if ($currentOffice->office_type_id == config('constant.HEAD_OFFICE')) {
                return $this->model
                    ->whereIn('status_id', [config('constant.CREATED_STATUS'), config('constant.RETURNED_STATUS')])
                    ->whereIn('office_id', $accessibleOfficeIds)
                    ->orWhere(function ($q) {
                        $q->whereNull('office_id');
                        $q->whereIn('status_id', [config('constant.CREATED_STATUS'), config('constant.RETURNED_STATUS')]);
                    })
                    ->orderBy('created_at', 'desc')->get();
            }
        }

        return $this->model
            ->whereIn('status_id', [config('constant.CREATED_STATUS'), config('constant.RETURNED_STATUS')])
            ->whereIn('office_id', $accessibleOfficeIds)
            ->orderBy('created_at', 'desc')->get();
    }

    public function create($inputs)
    {
        DB::beginTransaction();
        try {
            $agreement = $this->model->create($inputs);
            $this->transactions->createOpening($agreement);
            // dd($agreement, $agreement->transactions()->first()->toArray());
            DB::commit();

            return $agreement;
        } catch (QueryException $e) {
            DB::rollBack();

            // dd($e);
            return false;
        }
    }

    public function update($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $agreement = $this->model->findOrFail($id);
            $agreement->fill($inputs)->save();
            DB::commit();

            return $agreement;
        } catch (QueryException $e) {
            DB::rollBack();

            return false;
        }
    }

    public function submit($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $agreement = $this->model->find($id);
            $agreement->fill($inputs)->save();

            if ($inputs['btn'] == 'submit') {
                $inputs['status_id'] = 3;
                $agreement->update($inputs);
                $forwardInputs = [
                    'user_id' => $inputs['user_id'],
                    'log_remarks' => 'Attendance submitted. '.$inputs['remarks'],
                    'original_user_id' => $inputs['original_user_id'],
                    'status_id' => config('constant.SUBMITTED_STATUS'),
                ];
                $agreement->logs()->create($forwardInputs);
            }
            $agreement = $this->model->find($id);
            DB::commit();

            return $agreement;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();

            return false;
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $agreement = $this->model->findOrFail($id);
            $agreement->logs()->delete();
            foreach ($agreement->transactions as $transactions) {
                $transactions->logs()->delete();
            }
            $agreement->transactions()->delete();
            $agreement->delete();
            DB::commit();

            return $agreement;
        } catch (QueryException $e) {
            DB::rollBack();
            dd($e);

            return false;
        }
    }

    public function amend($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $agreement = $this->model->findOrFail($id);
            $agreement->status_id = config('constant.RETURNED_STATUS');
            $agreement->save();
            $agreement->logs()->create($inputs);
            DB::commit();

            return $agreement;
        } catch (QueryException $e) {
            DB::rollBack();

            return false;
        }
    }

    public function verify($agreementId, $inputs)
    {
        DB::beginTransaction();
        try {
            $agreement = $this->model->findOrFail($agreementId);
            $agreement->update($inputs);
            $agreement->logs()->create($inputs);
            DB::commit();

            return $agreement;
        } catch (QueryException $e) {
            DB::rollBack();

            return false;
        }
    }

    public function approve($agreementId, $inputs)
    {
        DB::beginTransaction();
        try {
            $agreement = $this->model->findOrFail($agreementId);
            $agreement->update($inputs);
            $agreement->logs()->create($inputs);
            DB::commit();

            return $agreement;
        } catch (QueryException $e) {
            DB::rollBack();

            return false;
        }
    }
}
