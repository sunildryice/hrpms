<?php

namespace Modules\TrainingRequest\Repositories;

use App\Repositories\Repository;
use Modules\TrainingRequest\Models\TrainingReportQuestion;

use DB;

class TrainingReportQuestionRepository extends Repository
{
    public function __construct(TrainingReportQuestion $trainingReportQuestion)
    {
        $this->model = $trainingReportQuestion;
    }
}
