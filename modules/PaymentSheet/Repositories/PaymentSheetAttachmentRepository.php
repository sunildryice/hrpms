<?php

namespace Modules\PaymentSheet\Repositories;
use App\Repositories\Repository;
use Modules\PaymentSheet\Models\PaymentSheetAttachment;

class PaymentSheetAttachmentRepository extends Repository
{
    public function __construct(
        PaymentSheetAttachment $paymentSheetAttachment
    )
    {
        $this->model = $paymentSheetAttachment;
    }

    
}