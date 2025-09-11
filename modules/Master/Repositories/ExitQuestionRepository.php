<?php
namespace Modules\Master\Repositories;

use App\Repositories\Repository;
use Modules\Master\Models\ExitQuestion;

class ExitQuestionRepository extends Repository
{
    public function __construct(ExitQuestion $question)
    {
        $this->model = $question;
    }
}
