<?php

namespace Modules\TransportationBill\Repositories;

use App\Repositories\Repository;
use Modules\Master\Repositories\FiscalYearRepository;
use Modules\TransportationBill\Models\TransportationBill;

use DB;

class TransportationBillRepository extends Repository
{
    public function __construct(
        FiscalYearRepository $fiscalYears,
        TransportationBill   $transportationBill
    )
    {
        $this->fiscalYears = $fiscalYears;
        $this->model = $transportationBill;
    }

    public function getApproved()
    {
        $authUser = auth()->user();
        $currentOffice = $authUser->getCurrentOffice();
        $accessibleOfficeIds = $authUser->getAccessibleOfficesIds();

        if ($currentOffice) {
            if ($currentOffice->office_type_id == config('constant.HEAD_OFFICE')) {
                return $this->model
                    ->with(['fiscalYear', 'status'])
                    ->select(['*'])
                    ->whereStatusId(config('constant.RECEIVED_STATUS'))
                    ->whereIn('office_id', $accessibleOfficeIds)
                    ->orWhere(function ($q) {
                        $q->whereNull('office_id');
                        $q->whereIn('status_id', [config('constant.APPROVED_STATUS')]);
                    })->orderBy('bill_date', 'desc')->get();
            }
        }

        return $this->model->with(['fiscalYear', 'status'])->select(['*'])
            ->whereStatusId(config('constant.RECEIVED_STATUS'))
            ->whereIn('office_id', $accessibleOfficeIds)
            ->orderBy('bill_date', 'desc')->get();
    }

    public function generateTransportationBillNumber($fiscalYearId)
    {
        $max = $this->model->select(['fiscal_year_id', 'bill_number'])
                ->where('fiscal_year_id', $fiscalYearId)
                ->max('bill_number') + 1;
        return $max;
    }

    public function approve($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $transportationBill = $this->model->find($id);
            if ($inputs['status_id'] == config('constant.RECOMMENDED_STATUS')) {
                $inputs['approver_id'] = $inputs['recommended_to'];
            }
            $transportationBill->update($inputs);
            $transportationBill->logs()->create($inputs);
            DB::commit();
            return $transportationBill;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }

    public function create($inputs)
    {
        DB::beginTransaction();
        try {
            $inputs['status_id'] = 1;
            $transportationBill = $this->model->create($inputs);
            DB::commit();
            return $transportationBill;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }

    public function destroy($id)
    {
        try {
            $transportationBill = $this->model->findOrFail($id);
            $transportationBill->logs()->delete();
            $transportationBill->transportationBillItems()->delete();
            $transportationBill->delete();
            return true;
        } catch (\Illuminate\Database\QueryException $e) {
            return false;
        }
    }

    public function forward($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $transportationBill = $this->model->findOrFail($id);
            $inputs['status_id'] = config('constant.SUBMITTED_STATUS');

            if (!$transportationBill->bill_number) {
                $fiscalYear = $this->fiscalYears->where('start_date', '<=', date('Y-m-d'))
                    ->where('end_date', '>=', date('Y-m-d'))
                    ->first();

                $inputs['fiscal_year_id'] = $fiscalYear->id;
                $inputs['prefix'] = 'TB';
                $inputs['bill_number'] = $this->generateTransportationBillNumber($fiscalYear->id);
            }
            $transportationBill->update($inputs);
            $transportationBill->logs()->create($inputs);
            DB::commit();
            return $transportationBill;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }

    public function update($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $transportationBill = $this->model->find($id);
            $transportationBill->fill($inputs)->save();
            if ($inputs['btn'] == 'submit') {
                $forwardInputs = [
                    'user_id' => $inputs['updated_by'],
                    'log_remarks' => 'Transportation bill is submitted.',
                    'original_user_id' => $inputs['original_user_id'],
                ];
                $transportationBill = $this->forward($transportationBill->id, $forwardInputs);
            }
            DB::commit();
            return $transportationBill;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }
}
