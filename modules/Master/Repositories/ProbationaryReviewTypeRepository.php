<?php
namespace Modules\Master\Repositories;

use App\Repositories\Repository;
use Modules\Master\Models\ProbationaryReviewType;

class ProbationaryReviewTypeRepository extends Repository
{
    public function __construct(ProbationaryReviewType $reviewType)
    {
        $this->model = $reviewType;
    }
}
