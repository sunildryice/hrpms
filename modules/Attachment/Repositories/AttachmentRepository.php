<?php

namespace Modules\Attachment\Repositories;

use Modules\Attachment\Models\Attachment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

class AttachmentRepository
{
    private $model;
    public function __construct(
        Attachment $attachment
    )
    {
        $this->model = $attachment;
    }

    public function create(Model $model, array $inputs)
    {
        DB::beginTransaction();
        try {
            $attachment = $model->attachments()->create($inputs);
            DB::commit();
            return $attachment;
        } catch (QueryException $e) {
            DB::rollBack();
            return false;
        }
    }

    public function update(Attachment $attachment, array $inputs)
    {
        DB::beginTransaction();
        try {
            $attachment->update($inputs);
            DB::commit();
            return $attachment->fresh();
        } catch (QueryException $e) {
            DB::rollBack();
            return false;
        }
    }

    public function destroy(Attachment $attachment)
    {
        DB::beginTransaction();
        try {
            $attachment->delete();
            DB::commit();
            return true;
        } catch (QueryException $e) {
            DB::rollBack();
            return false;
        }
    }

    public function find($id)
    {
        return $this->model->findOrFail($id);
    }
}