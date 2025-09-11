<?php
namespace Modules\Master\Repositories;

use App\Repositories\Repository;
use Modules\Master\Models\ExitRating;

class ExitRatingRepository extends Repository
{
    public function __construct(ExitRating $rating)
    {
        $this->model = $rating;
    }
}
