<?php
namespace Modules\Lta\Repositories;

use App\Repositories\Repository;
use Illuminate\Support\Facades\DB;
use Modules\Lta\Models\LtaContractItem;

class LtaContractItemRepository extends Repository
{
    public function __construct(LtaContractItem $ltaContractItems)
    {
        $this->model = $ltaContractItems;
    }
    
}
