<?php
namespace Modules\Master\Repositories;

use App\Repositories\Repository;
use Modules\Master\Models\ProbationaryIndicator;

class ProbationaryIndicatorRepository extends Repository
{
    public function __construct(ProbationaryIndicator $indicator)
    {
        $this->model = $indicator;
    }
}
