<?php

namespace Modules\Announcement\Repositories;

use Modules\Announcement\Models\Announcement;
use App\Repositories\Repository;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

class AnnouncementRepository extends Repository
{
    public function __construct(
        Announcement $announcement
    )
    {
        $this->model = $announcement;
    }

    public function getAnnouncementNumber()
    {
        return $this->model->max('announcement_number') + 1;
    }

    public function getActiveAnnouncements()
    {
        return $this->model->where('published_date', '<', now())->where('expiry_date', '>', now()->subDay())->orderBy('published_date', 'desc')->get();
    }

    public function create($inputs)
    {
        DB::beginTransaction();
        try {
            $announcement = $this->model->create($inputs);
            DB::commit();
            return $announcement;
        } catch (QueryException $e) {
            DB::rollBack();
            return false;
        }
    }

    public function update($id, $inputs)
    {
        DB::beginTransaction();
        try {
            $announcement = $this->model->findOrFail($id);
            $announcement->fill($inputs)->save();
            DB::commit();
            return $announcement;
        } catch (QueryException $e) {
            DB::rollBack();
            return false;
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $announcement = $this->model->findOrFail($id);
            $announcement->delete();
            DB::commit();
            return $announcement;
        } catch (QueryException $e) {
            DB::rollBack();
            return false;
        }
    }
}