<?php

namespace Modules\PurchaseRequest\Repositories;

use App\Repositories\Repository;
use Carbon\Carbon;
use Modules\Master\Models\FiscalYear;
use Modules\PurchaseRequest\Models\PurchaseRequest;
use DB;

class PurchaseRequestRepository extends Repository
{
    public function __construct(
        FiscalYear $fiscalYears,
        PurchaseRequest $purchaseRequest,
    )
    {
        $this->fiscalYears = $fiscalYears;
        $this->model = $purchaseRequest;
    }

    public function getApproved()
    {
        $authUser = auth()->user();
        $currentOffice = $authUser->getCurrentOffice();
        $accessibleOfficeIds = $authUser->getAccessibleOfficesIds();

        if ($currentOffice) {
            if ($currentOffice->office_type_id == config('constant.HEAD_OFFICE')) {
                return $this->model->with([
                    'procurementOfficers' => function ($q) {
                        $q->select(['id']);
                    },
                    'fiscalYear',
                    'status',
                    'requester' => function ($q) {
                        $q->select(['id', 'full_name']);
                    },
                    'purchaseRequestItems' => function ($q) {
                        $q->select(['purchase_request_id', 'total_price']);
                    }
                ])
                ->select(['*'])
                ->whereIn('status_id', [config('constant.APPROVED_STATUS'), config('constant.AMENDED_STATUS')])
                ->whereIn('office_id', $accessibleOfficeIds)
                ->orWhere(function ($q) {
                    $q->whereNull('office_id');
                    $q->whereIn('status_id', [config('constant.APPROVED_STATUS'), config('constant.AMENDED_STATUS')]);
                })
                ->orderBy('required_date', 'desc')
                ->get();
            }
        }

        return $this->model->with([
            'procurementOfficers' => function ($q) {
                $q->select(['id']);
            },
            'fiscalYear',
            'status',
            'requester' => function ($q) {
                $q->select(['id', 'full_name']);
            },
            'purchaseRequestItems' => function ($q) {
                $q->select(['purchase_request_id', 'total_price']);
            }
        ])
        ->select(['*'])
        ->whereIn('status_id', [config('constant.APPROVED_STATUS'), config('constant.AMENDED_STATUS')])
        ->whereIn('office_id', $accessibleOfficeIds)
        ->orderBy('required_date', 'desc')
        ->get();
    }

    public function getClosed()
    {
        $authUser = auth()->user();
        $currentOffice = $authUser->getCurrentOffice();
        $accessibleOfficeIds = $authUser->getAccessibleOfficesIds();

        if ($currentOffice) {
            if ($currentOffice->office_type_id == config('constant.HEAD_OFFICE')) {
                return $this->model->with(['fiscalYear', 'status', 'purchaseRequestItems'])
                ->select(['*'])
                ->whereIn('status_id', [config('constant.CLOSED_STATUS')])
                ->whereIn('office_id', $accessibleOfficeIds)
                ->orWhere(function ($q) {
                    $q->whereNull('office_id');
                    $q->whereIn('status_id', [config('constant.CLOSED_STATUS')]);
                })
                ->orderBy('required_date', 'desc')
                ->get();
            }
        }

        return $this->model->with(['fiscalYear', 'status', 'purchaseRequestItems'])
        ->select(['*'])
        ->whereIn('status_id',[config('constant.CLOSED_STATUS')])
        ->whereIn('office_id', $accessibleOfficeIds)
        ->orderBy('required_date', 'desc')
        ->get();
    }

    public function generatePurchaseRequestNumber($fiscalYearId)
    {
        $max = $this->model->select(['fiscal_year_id', 'purchase_number'])
                ->where('fiscal_year_id', $fiscalYearId)
                ->max('purchase_number') + 1;
        return $max;
    }

    public function getPurchaseRequestsForReviewAndApproval($authUser)
    {
        return $this->model->select(['*'])
            ->where(function ($q) use ($authUser){
                $q->where('status_id', config('constant.SUBMITTED_STATUS'))
                    ->where('reviewer_id', $authUser->id);
            })->orWhere(function ($q) use ($authUser){
                $q->whereIn('status_id', [config('constant.RECOMMENDED_STATUS'), config('constant.VERIFIED_STATUS')])
                    ->where('approver_id', $authUser->id);
            })->orderBy('request_date', 'desc')
            ->take(5)->get();
    }

    public function amend($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $purchaseRequest = $this->model->find($id);
            $purchaseRequest->update(['status_id' => config('constant.AMENDED_STATUS')]);
            $clone = $purchaseRequest->replicate();
            unset($clone->reviewer_id);
            unset($clone->verifier_id);
            unset($clone->recommender_id);
            unset($clone->approver_id);
            $clone->status_id = config('constant.CREATED_STATUS');
            $clone->request_date = date('Y-m-d');
            $clone->created_by = $inputs['created_by'];
            $clone->modification_purchase_request_id = $purchaseRequest->id;
            $parentPurchaseRequestId = $purchaseRequest->modification_purchase_request_id ?: $purchaseRequest->id;
            $clone->modification_number = $this->model->where('modification_purchase_request_id', $parentPurchaseRequestId)
                    ->max('modification_number') + 1;
            $clone->save();

            if($purchaseRequest->districts){
                $districtIds = $purchaseRequest->districts->map(function ($district) {
                    return $district->id;
                })->toArray();
                $clone->districts()->sync($districtIds);
            }

            foreach ($purchaseRequest->purchaseRequestItems as $purchaseRequestItem) {
                unset($purchaseRequestItem->id);
                unset($purchaseRequestItem->purchase_request_id);
                $purchaseRequestItemInputs = $purchaseRequestItem->toArray();
                $purchaseRequestItemInputs['created_by'] = $inputs['created_by'];
                $clone->purchaseRequestItems()->create($purchaseRequestItemInputs);
            }

            DB::commit();
            return $clone;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }

    public function approve($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $purchaseRequest = $this->model->find($id);
            if (in_array($inputs['status_id'],[config('constant.RECOMMENDED_STATUS'),config('constant.RECOMMENDED2_STATUS')])) {
                $inputs['recommender_id'] = $purchaseRequest->approver_id;
            }
            $purchaseRequest->update($inputs);
            $purchaseRequest->logs()->create($inputs);
            DB::commit();
            return $purchaseRequest;
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
            $purchaseRequest = $this->model->create($inputs);
            if(!empty($inputs['procurement_officer'])){
                $purchaseRequest->procurementOfficers()->sync($inputs['procurement_officer']);
            }
            $inputs['user_id'] = auth()->user()->id;
            $inputs['log_remarks'] = 'Purchase request created.';
            $purchaseRequest->logs()->create($inputs);
            DB::commit();
            return $purchaseRequest;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }

    public function destroy($id)
    {
        try {
            $purchaseRequest = $this->model->findOrFail($id);

            if ($purchaseRequest->parentPurchaseRequest) {
                $parentPurchaseRequest = $purchaseRequest->parentPurchaseRequest;
                $parentPurchaseRequest->update(['status_id' => config('constant.APPROVED_STATUS')]);
            }
            $purchaseRequest->procurementOfficers()->sync([]);
            $purchaseRequest->logs()->delete();
            $purchaseRequest->purchaseRequestItems()->delete();
            $purchaseRequest->delete();
            return true;
        } catch (\Illuminate\Database\QueryException $e) {
            return false;
        }
    }

    public function forward($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $purchaseRequest = $this->model->findOrFail($id);
            if (!$purchaseRequest->purchase_number) {
                $inputs['prefix'] = 'PR';
                $inputs['purchase_number'] = $this->generatePurchaseRequestNumber($inputs['fiscal_year_id']);
            }

            $purchaseRequestItems = $purchaseRequest->purchaseRequestItems;
            $purchaseRequestBudgets = $purchaseRequest->purchaseRequestBudgets;

            if(!empty($purchaseRequestBudgets) && !empty($purchaseRequestItems)){
                foreach ($purchaseRequestBudgets as $purchaseRequestBudget) {
                    foreach($purchaseRequestItems as $item){
                        if($purchaseRequestBudget->office_id == $item->office_id && $purchaseRequestBudget->activity_code_id == $item->activity_code_id){
                            continue;
                        }
                        $purchaseRequestBudget->delete();
                    }
                }
            }
            if ($purchaseRequestItems->isNotEmpty()) {
                foreach ($purchaseRequestItems as $purchaseRequestItem) {
                    if (!empty($purchaseRequestItem->office_id) && !empty($purchaseRequestItem->activity_code_id)) {
                        $purchaseRequest->purchaseRequestBudgets()->updateOrCreate(
                            [
                                // 'district_id' => $purchaseRequestItem->district_id,
                                'office_id' => $purchaseRequestItem->office_id,
                                'activity_code_id' => $purchaseRequestItem->activity_code_id
                            ],
                            [
                                'created_by' => auth()->user()->id
                            ]
                        );
                    }
                }
            }

            $purchaseRequest->update($inputs);
            $inputs['status_id'] = config('constant.SUBMITTED_STATUS');
            $purchaseRequest->logs()->create($inputs);
            $this->updateTotalAmount($id);
            DB::commit();
            return $purchaseRequest;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }

    public function replicate($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $purchaseRequest = $this->model->find($id);
            $clone = $purchaseRequest->replicate();
            unset($clone->reviewer_id);
            unset($clone->verifier_id);
            unset($clone->recommender_id);
            unset($clone->approver_id);
            unset($clone->prefix);
            unset($clone->purchase_number);
            unset($clone->modification_number);
            unset($clone->modification_purchase_request_id);
            unset($clone->required_date);
            $clone->status_id = config('constant.CREATED_STATUS');
            $clone->request_date = date('Y-m-d');
            $clone->required_date = Carbon::now()->addDays(10)->format('Y-m-d');
            $clone->created_by = $inputs['created_by'];
            $clone->save();

            foreach ($purchaseRequest->purchaseRequestItems as $purchaseRequestItem) {
                unset($purchaseRequestItem->id);
                unset($purchaseRequestItem->purchase_request_id);
                $purchaseRequestItemInputs = $purchaseRequestItem->toArray();
                $purchaseRequestItemInputs['created_by'] = $inputs['created_by'];
                $clone->purchaseRequestItems()->create($purchaseRequestItemInputs);
            }

            DB::commit();
            return $clone;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }

    public function review($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $purchaseRequest = $this->model->find($id);
            foreach ($purchaseRequest->purchaseRequestBudgets as $prBudget) {
                $prBudget->update([
                    'balance_budget'            => $inputs['balance_budget'][$prBudget->id],
                    'commitment_amount'         => $inputs['commitment_amount'][$prBudget->id],
                    'estimated_balance_budget'  => $inputs['balance_budget'][$prBudget->id] - $inputs['commitment_amount'][$prBudget->id],
                    'budgeted'                  => $inputs['budgeted'][$prBudget->id],
                    'description'               => $inputs['description'][$prBudget->id],
                    'updated_by'                => auth()->user()->id
                ]);
            }

            unset($inputs['balance_budget']);
            unset($inputs['commitment_amount']);
            unset($inputs['budgeted']);

            if ($inputs['btn'] == 'submit') {
                $purchaseRequest->update($inputs);
                $purchaseRequest->logs()->create($inputs);
            }
            DB::commit();
            return $purchaseRequest;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }

    public function reviewRecommended($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $purchaseRequest = $this->model->find($id);
            $purchaseRequest->update($inputs);
            $purchaseRequest->logs()->create($inputs);
            DB::commit();
            return $purchaseRequest;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }

    public function update($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $purchaseRequest = $this->model->find($id);
            $purchaseRequest->fill($inputs)->save();
            if(!empty($inputs['procurement_officer'])){
                $purchaseRequest->procurementOfficers()->sync($inputs['procurement_officer']);
            }else{
                $purchaseRequest->procurementOfficers()->sync([]);
            }
            DB::commit();
            return $purchaseRequest;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }

    public function updateTotalAmount($purchaseRequestId)
    {
        DB::beginTransaction();
        try {
            $purchaseRequest = $this->model->find($purchaseRequestId);
            $subTotal = $purchaseRequest->purchaseRequestItems->sum('total_price');
            $updateInputs = [
                'total_amount' => $subTotal,
            ];
            $purchaseRequest->update($updateInputs);
            DB::commit();
            return $purchaseRequest;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }

    public function close($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $purchaseRequest = $this->model->find($id);
            $inputs['status_id'] = config('constant.CLOSED_STATUS');
            $inputs['updated_by'] = auth()->user()->id;
            $inputs['closed_at'] = date('Y-m-d H:i:s');
            $purchaseRequest->update($inputs);
            $inputs['user_id'] = $inputs['updated_by'];
            $inputs['log_remarks'] = 'Purchase request closed.';
            $purchaseRequest->logs()->create($inputs);
            DB::commit();
            return $purchaseRequest;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            return false;
        }
    }

    public function open($id, $inputs)
    {
       DB::beginTransaction();
        try {
            $purchaseRequest = $this->model->find($id);
            $inputs['status_id'] = config('constant.APPROVED_STATUS');
            $inputs['updated_by'] = auth()->user()->id;
            $inputs['closed_at'] = null;
            $purchaseRequest->update($inputs);
            $inputs['user_id'] = $inputs['updated_by'];
            $inputs['log_remarks'] = 'Purchase request opened.';
            $purchaseRequest->logs()->create($inputs);
            DB::commit();
            return $purchaseRequest;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            return false;
        }
    }

    public function verify($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $purchaseRequest = $this->model->find($id);
            $purchaseRequest->fill($inputs)->save();
            $purchaseRequest->logs()->create($inputs);
            DB::commit();
            return $purchaseRequest;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }


}
