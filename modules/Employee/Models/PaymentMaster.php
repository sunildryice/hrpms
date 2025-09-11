<?php

namespace Modules\Employee\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\ModelEventLogger;
use Modules\Payroll\Models\PaymentItem;
use Modules\Privilege\Models\User;

class PaymentMaster extends Model
{
    use HasFactory, ModelEventLogger;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'employee_payment_masters';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'employee_id',
        'payment_item_id',
        'start_date',
        'end_date',
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
     * Get the employee of the payment master.
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    /**
     * Get the payment details of the payment master.
     */
    public function paymentDetails()
    {
        return $this->hasMany(PaymentDetail::class, 'payment_master_id');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by')->withDefault();
    }

    public function getCreatedBy()
    {
        return $this->createdBy->getFullName();
    }

    public function getEndDate()
    {
        return $this->end_date->toFormattedDateString();
    }

    public function getPaymentItem()
    {
        return $this->paymentItem->title;
    }

    public function getStartDate()
    {
        return $this->start_date->toFormattedDateString();
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
