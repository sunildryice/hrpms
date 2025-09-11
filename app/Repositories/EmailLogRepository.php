<?php
namespace App\Repositories;

use App\Models\EmailLog;

class EmailLogRepository extends Repository
{
    public function __construct(EmailLog $log)
    {
        $this->model = $log;
    }
}
