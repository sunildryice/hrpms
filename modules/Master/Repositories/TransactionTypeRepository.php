<?php
namespace Modules\Master\Repositories;

use App\Repositories\Repository;
use Modules\Master\Models\TransactionType;

class TransactionTypeRepository extends Repository
{
    public function __construct(TransactionType $transactionType)
    {
        $this->model = $transactionType;
    }
}
