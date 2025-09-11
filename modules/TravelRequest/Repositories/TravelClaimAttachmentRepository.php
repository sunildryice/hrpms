<?php

namespace Modules\TravelRequest\Repositories;

use App\Repositories\Repository;
use Modules\TravelRequest\Models\TravelClaimAttachment;

use DB;

class TravelClaimAttachmentRepository extends Repository
{
    public function __construct(TravelClaimAttachment $travelClaimAttachment)
    {
        $this->model = $travelClaimAttachment;
    }
}
