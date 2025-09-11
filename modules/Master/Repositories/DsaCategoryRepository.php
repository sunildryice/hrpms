<?php
namespace Modules\Master\Repositories;

use App\Repositories\Repository;
use Modules\Master\Models\DsaCategory;

class DsaCategoryRepository extends Repository
{
    public function __construct(DsaCategory $dsaCategory)
    {
        $this->model = $dsaCategory;
    }
}
