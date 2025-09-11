<?php
namespace Modules\Master\Repositories;

use App\Repositories\Repository;
use Modules\Master\Models\TrainingQuestion;

class TrainingQuestionRepository extends Repository
{
    public function __construct(TrainingQuestion $question)
    {
        $this->model = $question;
    }
}
