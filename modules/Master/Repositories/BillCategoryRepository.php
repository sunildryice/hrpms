<?php
namespace Modules\Master\Repositories;

use App\Repositories\Repository;
use Modules\Master\Models\BillCategory;

class BillCategoryRepository extends Repository
{
    public function __construct(BillCategory $category)
    {
        $this->model = $category;
    }

    public function getActiveCategories()
    {
        return $this->model->select(['id', 'title'])
            ->whereNotNull('activated_at')
            ->orderBy('title')
            ->get();
    }
}
