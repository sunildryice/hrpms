<?php
namespace Modules\Master\Repositories;

use App\Repositories\Repository;
use Modules\Master\Models\ExitFeedback;

class ExitFeedbackRepository extends Repository
{
    public function __construct(ExitFeedback $feedback)
    {
        $this->model = $feedback;
    }
}
