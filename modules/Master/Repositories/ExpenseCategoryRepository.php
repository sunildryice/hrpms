<?php
namespace Modules\Master\Repositories;

use App\Repositories\Repository;
use Modules\Master\Models\ExpenseCategory;

class ExpenseCategoryRepository extends Repository
{
    public function __construct(ExpenseCategory $category)
    {
        $this->model = $category;
    }
}
