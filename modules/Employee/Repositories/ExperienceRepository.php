<?php
namespace Modules\Employee\Repositories;

use App\Repositories\Repository;
use Modules\Employee\Models\Experience;

class ExperienceRepository extends Repository
{
    public function __construct(Experience $experience)
    {
        $this->model = $experience;
    }
}
