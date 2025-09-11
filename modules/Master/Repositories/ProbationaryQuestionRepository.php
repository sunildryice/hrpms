<?php
namespace Modules\Master\Repositories;

use App\Repositories\Repository;
use Modules\Master\Models\ProbationaryQuestion;

class ProbationaryQuestionRepository extends Repository
{
    public function __construct(ProbationaryQuestion $question)
    {
        $this->model = $question;
    }
}
