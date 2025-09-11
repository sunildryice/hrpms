<?php
namespace Modules\Employee\Repositories;

use App\Repositories\Repository;
use Modules\Employee\Models\Finance;

class FinanceRepository extends Repository
{
    public function __construct(Finance $finance)
    {
        $this->model = $finance;
    }
}
