<?php

namespace Modules\Inventory\Repositories;

use App\Repositories\Repository;
use Modules\Inventory\Models\AssetAssignLog;

class AssetAssignLogRepository extends Repository
{
    public function __construct(AssetAssignLog $assetAssignLog)
    {
        $this->model = $assetAssignLog;
    }

}
