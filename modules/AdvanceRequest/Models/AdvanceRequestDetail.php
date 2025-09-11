<?php

namespace Modules\AdvanceRequest\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\ModelEventLogger;
use Modules\Master\Models\District;
use Modules\Master\Models\FiscalYear;
use Modules\Master\Models\ActivityCode;
use Modules\Master\Models\AccountCode;
use Modules\Master\Models\DonorCode;
use Modules\Master\Models\Office;
use Modules\Master\Models\Status;
use Modules\Privilege\Models\User;

class AdvanceRequestDetail extends Model
{
    use HasFactory, ModelEventLogger;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'advance_request_details';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'advance_request_id',
        'account_code_id',
        'activity_code_id',
        'donor_code_id',
        'description',
        'amount',
        'attachment',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];

   /**
     * Get the advance order of the advance order item.
     */
    public function advanceRequest()
    {
        return $this->belongsTo(AdvanceRequest::class, 'advance_request_id');
    }

     /**
     * Get the accountCode of the purchase order item.
     */
    public function accountCode()
    {
        return $this->belongsTo(AccountCode::class, 'account_code_id')->withDefault();
    }

      /**
     * Get the activityCode of the purchase order item.
     */
    public function activityCode()
    {
        return $this->belongsTo(ActivityCode::class, 'activity_code_id')->withDefault();
    }


    /**
     * Get the donorCode of the purchase order item.
     */
    public function donorCode()
    {
        return $this->belongsTo(DonorCode::class, 'donor_code_id')->withDefault();
    }

     public function getActivityCode()
    {
        return $this->activityCode->getActivityCodeWithDescription();
    }

    public function getAccountCode()
    {
        return $this->accountCode->getAccountCodeWithDescription();
    }

    public function getDonorCode()
    {
        return $this->donorCode->getDonorCodeWithDescription();
    }

}
