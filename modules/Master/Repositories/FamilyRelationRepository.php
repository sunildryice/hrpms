<?php
namespace Modules\Master\Repositories;

use App\Repositories\Repository;
use Modules\Master\Models\FamilyRelation;

class FamilyRelationRepository extends Repository
{
    public function __construct(FamilyRelation $familyRelation)
    {
        $this->model = $familyRelation;
    }
}
