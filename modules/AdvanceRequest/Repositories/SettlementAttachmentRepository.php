<?php

namespace Modules\AdvanceRequest\Repositories;
use App\Repositories\Repository;
use Modules\AdvanceRequest\Models\SettlementAttachment;

class SettlementAttachmentRepository extends Repository
{
    public function __construct(
        SettlementAttachment $settlementAttachment
    )
    {
        $this->model = $settlementAttachment;
    }

    
}