<?php
namespace App\Repositories;

use App\Models\AuditLog;

class AuditLogRepository extends Repository
{
    public function __construct(AuditLog $auditLog)
    {
        $this->model = $auditLog;
    }
}
