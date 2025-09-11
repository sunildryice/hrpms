<?php

namespace Modules\ConstructionTrack\Repositories;
use App\Repositories\Repository;
use Modules\ConstructionTrack\Models\ConstructionAmendment;

class ConstructionAmendmentRepository extends Repository
{
    public function __construct(
        ConstructionAmendment $constructionAmendment
    )
    {
        $this->model = $constructionAmendment;
    }

    
}