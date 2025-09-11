<?php
namespace Modules\Master\Repositories;

use App\Repositories\Repository;
use Modules\Master\Models\AccountCode;

class AccountCodeRepository extends Repository
{
    public function __construct(AccountCode $accountCode)
    {
        $this->model = $accountCode;
    }
}
