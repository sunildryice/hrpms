<?php

namespace Modules\Employee\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\ModelEventLogger;
use Modules\Payroll\Models\PaymentItem;
use Modules\Privilege\Models\User;

class PaymentDetail extends Model
{
    use HasFactory, ModelEventLogger;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'employee_payment_master_details';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'payment_master_id',
        'payment_item_id',
        'amount',
        'created_by',
        'updated_by',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];

    protected $dates = ['start_date', 'end_date'];

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by')->withDefault();
    }

    /**
     * Get the payment item of the payment master.
     */
    public function paymentItem()
    {
        return $this->belongsTo(PaymentItem::class, 'payment_item_id')->withDefault();
    }

    /**
     * Get the payment master of the payment detail.
     */
    public function paymentMaster()
    {
        return $this->belongsTo(PaymentMaster::class, 'payment_master_id');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by')->withDefault();
    }

    public function getCreatedBy()
    {
        return $this->createdBy->getFullName();
    }

    public function getPaymentItem()
    {
        return $this->paymentItem->title;
    }

    public function getUpdatedAt()
    {
        return $this->updated_at->toFormattedDateString();
    }

    public function getUpdatedBy()
    {
        return $this->updatedBy->getFullName();
    }
}
