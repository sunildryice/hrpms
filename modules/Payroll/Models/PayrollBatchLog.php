<?php

namespace Modules\Payroll\Models;

use App\Traits\ModelEventLogger;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Master\Models\Status;
use Modules\Privilege\Models\User;

class PayrollBatchLog extends Model
{
    use HasFactory, ModelEventLogger;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'payroll_batch_logs';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'payroll_batch_id',
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
     * Get the createdBy of the log.
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'user_id')->withDefault();
    }

    /**
     * Get the payroll batch of the log.
     */
    public function payrollBatch()
    {
        return $this->belongsTo(PayrollBatch::class, 'payroll_batch_id');
    }


    /**
     * Get the status of the purchase request log.
     */
    public function status()
    {
        return $this->belongsTo(Status::class, 'status_id')->withDefault();
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
