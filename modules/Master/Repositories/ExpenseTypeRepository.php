<?php
namespace Modules\Master\Repositories;

use App\Repositories\Repository;
use Modules\Master\Models\ExpenseType;

class ExpenseTypeRepository extends Repository
{
    public function __construct(ExpenseType $type)
    {
        $this->model = $type;
    }
}
