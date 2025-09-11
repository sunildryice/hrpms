<?php
namespace Modules\Master\Repositories;

use App\Repositories\Repository;
use Modules\Master\Models\MaritalStatus;

class MaritalStatusRepository extends Repository
{
    public function __construct(MaritalStatus $maritalStatus)
    {
        $this->model = $maritalStatus;
    }
}
