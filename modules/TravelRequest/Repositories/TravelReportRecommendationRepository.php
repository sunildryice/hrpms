<?php

namespace Modules\TravelRequest\Repositories;

use App\Repositories\Repository;
use Modules\TravelRequest\Models\TravelReportRecommendation;

use DB;

class TravelReportRecommendationRepository extends Repository
{
    public function __construct(TravelReportRecommendation $travelReportRecommendation)
    {
        $this->model = $travelReportRecommendation;
    }
}
