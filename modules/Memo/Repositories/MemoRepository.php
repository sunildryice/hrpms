<?php

namespace Modules\Memo\Repositories;

use App\Repositories\Repository;
use DB;
use Modules\Master\Models\FiscalYear;
use Modules\Memo\Models\Memo;

class MemoRepository extends Repository
{
    public function __construct(
        FiscalYear $fiscalYear,
        Memo $memo
    ) {
        $this->fiscalYear = $fiscalYear;
        $this->model = $memo;
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
                    ->orderBy('created_at', 'desc')
                    ->get();
            }
        }

        return $this->model
            ->whereIn('status_id', [config('constant.APPROVED_STATUS')])
            ->whereIn('office_id', $accessibleOfficeIds)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getMemoNumber($fiscalYear)
    {
        $max = $this->model->select(['fiscal_year_id', 'memo_number'])
            ->where('fiscal_year_id', $fiscalYear->id)
            ->max('memo_number') + 1;
        return $max;
    }

    public function approve($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $memo = $this->model->find($id);
            $memo->update($inputs);
            $memo->logs()->create($inputs);
            DB::commit();
            return $memo;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }

    public function create($inputs)
    {
        DB::beginTransaction();
        try {
            $memo = $this->model->create($inputs);
            $memo->to()->sync($inputs['memo_to']);
            if (array_key_exists('memo_through', $inputs)) {
                $memo->memoThrough()->sync($inputs['memo_through']);
            }
            if ($inputs['btn'] == 'submit') {
                $forwardInputs = [
                    'log_remarks' => 'Memo is submitted.',
                    'status_id' => config('constant.SUBMITTED_STATUS'),
                    'user_id' => $memo->created_by,
                    'original_user_id' => $inputs['original_user_id'],
                ];
                $memo = $this->forward($memo->id, $forwardInputs);
            }
            DB::commit();
            return $memo;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }

    public function forward($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $memo = $this->model->findOrFail($id);
            if (!$memo->memo_number) {
                $fiscalYear = $this->fiscalYear->where('start_date', '<=', date('Y-m-d'))
                    ->where('end_date', '>=', date('Y-m-d'))
                    ->first();

                $inputs['fiscal_year_id'] = $fiscalYear->id;
                $inputs['prefix'] = 'MEMO';
                $inputs['memo_number'] = $this->getMemoNumber($fiscalYear);
                $inputs['submitted_at'] = date('Y-m-d H:i:s');
            }
            $memo->update($inputs);
            $memo->logs()->create($inputs);
            DB::commit();
            $memo = $this->model->find($id);
            return $memo;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }

    }

    public function update($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $memo = $this->model->find($id);
            $memo->fill($inputs)->save();
            $memo->to()->sync($inputs['memo_to']);
            if ($inputs['memo_through'][0] != null) {
                $memo->memoThrough()->sync($inputs['memo_through']);
            } else {
                $memo->memoThrough()->sync([]);
            }
            if ($inputs['btn'] == 'submit') {
                $forwardInputs = [
                    'log_remarks' => 'Memo is submitted.',
                    'status_id' => 3,
                    'user_id' => $memo->created_by,
                    'original_user_id' => $inputs['original_user_id'],
                ];
                $memo = $this->forward($memo->id, $forwardInputs);
            }
            DB::commit();
            return $memo;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }

    public function amend($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $memo = $this->model->find($id);
            $memo->update(['status_id' => config('constant.AMENDED_STATUS')]);
            $clone = $memo->replicate();
            unset($clone->submitted_at);
            $clone->status_id = config('constant.CREATED_STATUS');
            $clone->created_by = $inputs['created_by'];
            $clone->modification_memo_id = $memo->id;
            $parentMemoId = $memo->modification_memo_id ?: $memo->id;
            $clone->modification_number = $this->model->where('modification_memo_id', $parentMemoId)
                ->max('modification_number') + 1;
            $clone->save();

            if ($to = $memo->to) {
                $toIds = $to->pluck('id')->toArray();
                $clone->to()->sync($toIds);
            }
            if ($through = $memo->through) {
                $throughIds = $through->pluck('id')->toArray();
                $clone->memoThrough()->sync($throughIds);
            }

            DB::commit();
            return $clone;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $memo = $this->model->findOrFail($id);
            if ($memo->parentMemo) {
                $parentMemo = $memo->parentMemo;
                $parentMemo->update(['status_id' => config('constant.APPROVED_STATUS')]);
            }
            $memo->to()->sync([]);
            $memo->memoThrough()->sync([]);
            $memo->logs()->delete();
            $memo->delete();
            DB::commit();
            return true;
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollback();
            return false;
        }
    }

}
