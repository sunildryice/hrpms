<?php
namespace Modules\Contract\Repositories;

use App\Repositories\Repository;
use Modules\Contract\Models\ContractAmendment;

use DB;

class ContractAmendmentRepository extends Repository
{
    public function __construct(ContractAmendment $contractAmendment)
    {
        $this->model = $contractAmendment;
    }
}
