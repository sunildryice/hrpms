<?php

namespace Modules\Inventory\Repositories;

use App\Repositories\Repository;

use Modules\Inventory\Models\AssetConditionLog;

class AssetConditionLogRepository extends Repository
{
    public function __construct(AssetConditionLog $assetConditionLog)
    {
        $this->model = $assetConditionLog;
    }

}
