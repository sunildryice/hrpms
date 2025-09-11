<?php
namespace Modules\Master\Repositories;

use App\Repositories\Repository;
use Modules\Master\Models\LocalLevel;

class LocalLevelRepository extends Repository
{
    public function __construct(LocalLevel $locallevel)
    {
        $this->model = $locallevel;
    }
}
