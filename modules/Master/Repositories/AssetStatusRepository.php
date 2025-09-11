<?php
namespace Modules\Master\Repositories;

use App\Repositories\Repository;
use Modules\Master\Models\AssetStatus;

class AssetStatusRepository extends Repository
{
    public function __construct(AssetStatus $assetStatus)
    {
        $this->model = $assetStatus;
    }

    public function getStatuses()
    {
        return $this->model->get();
    }
}
