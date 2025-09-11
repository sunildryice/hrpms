<?php
namespace Modules\Master\Repositories;

use App\Repositories\Repository;
use Modules\Master\Models\OfficeType;

class OfficeTypeRepository extends Repository
{
    public function __construct(OfficeType $officeType)
    {
        $this->model = $officeType;
    }
}
