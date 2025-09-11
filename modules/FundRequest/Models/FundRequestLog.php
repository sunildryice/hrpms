<?php

namespace Modules\FundRequest\Models;

use App\Traits\ModelEventLogger;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Master\Models\Status;
use Modules\Privilege\Models\User;

class FundRequestLog extends Model
{
    use HasFactory, ModelEventLogger;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'fund_request_logs';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'fund_request_id',
        'user_id',
        'original_user_id',
        'log_remarks',
        'status_id',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];

    /**
     * Get the fund request of the log.
     */
    public function fundRequest()
    {
        return $this->belongsTo(FundRequest::class, 'fund_request_id');
    }

    /**
     * Get the createdBy of the log.
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'user_id')->withDefault();
    }

    /**
     * Get the status of the fund request log.
     */
    public function status()
    {
        return $this->belongsTo(Status::class, 'status_id')->withDefault();
    }

    public function getCreatedAt()
    {
        return $this->created_at;
    }

    public function getCreatedDate()
    {
        return $this->created_at->toFormattedDateString();
    }

    public function getCreatedBy()
    {
        return $this->createdBy->getFullName();
    } 

    public function getDesignation()
    {
        return $this->createdBy->employee->tenures()->where('created_at', '<=', $this->created_at)->latest()->first()->getDesignationName() ?? '';
    }

}
