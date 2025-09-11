<?php

namespace Modules\TransportationBill\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\ModelEventLogger;
use Modules\Master\Models\District;
use Modules\Master\Models\FiscalYear;
use Modules\Master\Models\Office;
use Modules\Master\Models\ProjectCode;
use Modules\Master\Models\Status;
use Modules\Privilege\Models\User;

class TransportationBill extends Model
{
    use HasFactory, ModelEventLogger;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'transportation_bills';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'fiscal_year_id',
        'office_id',
        'prefix',
        'bill_number',
        'bill_date',
        'shipper_name',
        'shipper_address',
        'consignee_name',
        'consignee_address',
        'remarks',
        'instruction',
        'reviewer_id',
        'approver_id',
        'status_id',
        'created_by',
        'updated_by',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];

    protected $dates = ['bill_date'];

    /**
     * Get the alternate receiver of a transportation bill
     */
    public function alternateReceiver()
    {
        return $this->belongsTo(User::class, 'reviewer_id')->withDefault();
    }

    /**
     * Get the office of the employee.
     */
    public function office()
    {
        return $this->belongsTo(Office::class, 'office_id')->withDefault();
    }

    /**
     * Get the fiscal year.
     */
    public function fiscalYear()
    {
        return $this->belongsTo(Fiscalyear::class, 'fiscal_year_id')->withDefault();
    }

    /**
     * Get the receiver of a transportation bill
     */
    public function receiver()
    {
        return $this->belongsTo(User::class, 'approver_id')->withDefault();
    }

    /**
     * Get requester of the fund request.
     */
    public function requester()
    {
        return $this->belongsTo(User::class, 'created_by')->withDefault();
    }

    /**
     * Get the fund request status.
     */
    public function status()
    {
        return $this->belongsTo(Status::class, 'status_id')->withDefault();
    }

    /**
     * Get the logs for the fund request.
     */
    public function logs()
    {
        return $this->hasMany(TransportationBillLog::class, 'transportation_bill_id')
            ->orderBy('created_at', 'desc');
    }

    /**
     * Get the received log for the transportation bill.
     */
    public function receivedLog()
    {
        return $this->hasOne(TransportationBillLog::class, 'transportation_bill_id')
            ->whereStatusId(config('constant.RECEIVED_STATUS'))
            ->latest();
    }

    /**
     * Get the transportation bill details for the transportation bill.
     */
    public function transportationBillDetails()
    {
        return $this->hasMany(TransportationBillDetail::class, 'transportation_bill_id');
    }

    public function getAlternateReceiverName()
    {
        return $this->alternateReceiver->getFullName();
    }

    public function getBillDate()
    {
        return $this->bill_date ? $this->bill_date->toFormattedDateString() : "";
    }

    public function getFiscalYear()
    {
        return $this->fiscalYear->title;
    }

    public function getOfficeName()
    {
        return $this->office->getOfficeName();
    }

    public function getRequesterName()
    {
        return $this->requester->getFullName();
    }

    public function getReceiverName()
    {
        return $this->receiver->getFullName();
    }

    public function getStatus()
    {
        return ucwords($this->status->title);
    }

    public function getStatusClass()
    {
        return $this->status->status_class;
    }

    public function getTransportationBillNumber()
    {
        return $this->bill_number;
    }
}
