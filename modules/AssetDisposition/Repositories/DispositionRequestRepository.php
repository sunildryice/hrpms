<?php

namespace Modules\AssetDisposition\Repositories;

use App\Repositories\Repository;
use DB;
use Illuminate\Support\Facades\Auth;
use Modules\AssetDisposition\Models\DispositionRequest;

class DispositionRequestRepository extends Repository
{
    private $fiscalYears;

    public function __construct(
        DispositionRequest $dispositionRequest
    ) {
        $this->model = $dispositionRequest;
    }

    public function getApproved()
    {
        $authUser = Auth::user();
        $currentOffice = $authUser->getCurrentOffice();
        $accessibleOfficeIds = $authUser->getAccessibleOfficesIds();

        if ($currentOffice) {
            if ($currentOffice->office_type_id == config('constant.HEAD_OFFICE')) {
                return $this->model->with(['requester', 'status', 'office', 'disposeAssets.asset'])
                    ->select(['*'])
                    ->whereIn('status_id', [config('constant.APPROVED_STATUS')])
                    ->whereIn('office_id', $accessibleOfficeIds)
                    ->orWhere(function ($q) {
                        $q->whereNull('office_id');
                        $q->whereIn('status_id', [config('constant.APPROVED_STATUS')]);
                    })
                    ->orderBy('created_at', 'desc')->get();
            }
        }

        return $this->model->with(['requester', 'status', 'asset', 'office', 'disposeAssets.asset'])
            ->select(['*'])
            ->whereIn('status_id', [config('constant.APPROVED_STATUS')])
            ->whereIn('office_id', $accessibleOfficeIds)
            ->orderBy('created_at', 'desc')->get();
    }

    public function approve($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $assetDisposition = $this->model->find($id);
            if ($inputs['status_id'] == config('constant.RECOMMENDED_STATUS')) {
                $inputs['approver_id'] = $inputs['recommended_to'];
                $inputs['reviewer_id'] = $assetDisposition->approver_id;
            }
            $assetDisposition->update($inputs);
            $assetDisposition->logs()->create($inputs);
            if ($assetDisposition->status_id == config('constant.APPROVED_STATUS')) {
                foreach ($assetDisposition->disposeAssets as $disposedAsset) {
                    $disposedAsset->asset()->update([
                        'status' => config('constant.ASSET_DISPOSED'),
                    ]);
                }
            }
            DB::commit();

            return $assetDisposition;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();

            return false;
        }
    }

    public function cancel($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $assetDisposition = $this->model->find($id);
            $inputs['status_id'] = config('constant.CANCELLED_STATUS');
            $assetDisposition->update($inputs);
            $assetDisposition->logs()->create($inputs);
            DB::commit();

            return $assetDisposition;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();

            return false;
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $assetDisposition = $this->model->findOrFail($id);
            $assetDisposition->logs()->delete();
            $assetDisposition->delete();
            DB::commit();

            return true;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();

            return false;
        }
    }

    public function forward($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $assetDisposition = $this->model->findOrFail($id);
            $inputs['status_id'] = config('constant.SUBMITTED_STATUS');
            $assetDisposition->update($inputs);
            $assetDisposition->logs()->create($inputs);
            DB::commit();

            return $assetDisposition;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();

            return false;
        }
    }

    public function update($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $dispositionRequest = $this->model->find($id);
            $dispositionRequest->fill($inputs)->save();
            $dispositionRequest->disposeAssets()->delete();
            if (! empty($inputs['asset_input'])) {
                foreach ($inputs['asset_input'] as $disposeAsset) {
                    $dispositionRequest->disposeAssets()->create($disposeAsset);
                }
            }

            if ($inputs['btn'] == 'submit') {
                $forwardInputs = [
                    'user_id' => $inputs['updated_by'],
                    'log_remarks' => 'Disposition Request  is submitted.',
                    'original_user_id' => $inputs['original_user_id'],
                ];
                $dispositionRequest = $this->forward($dispositionRequest->id, $forwardInputs);
            }
            DB::commit();

            return $dispositionRequest;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();

            return false;
        }
    }
}
