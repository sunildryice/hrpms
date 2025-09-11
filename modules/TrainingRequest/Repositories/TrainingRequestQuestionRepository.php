<?php
namespace Modules\TrainingRequest\Repositories;

use App\Repositories\Repository;
use Modules\TrainingRequest\Models\TrainingRequestQuestion;

class TrainingRequestQuestionRepository extends Repository
{
    public function __construct(TrainingRequestQuestion $trainingRequestQuestion)
    {
        $this->model = $trainingRequestQuestion;
    }
}
