<?php

namespace Modules\FundRequest\Models;

use App\Traits\ModelEventLogger;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Master\Models\ActivityCode;

class FundRequestActivity extends Model
{
    use HasFactory, ModelEventLogger;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'fund_request_activities';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'fund_request_id',
        'activity_code_id',
        'estimated_amount',
        'budget_amount',
        'variance_budget_amount',
        'project_target_unit',
        'dip_target_unit',
        'variance_target_unit',
        'justification_note',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];

    /**
     * Get the fund request of the fund request activity.
     */
    public function fundRequest()
    {
        return $this->belongsTo(FundRequest::class, 'fund_request_id');
    }

    /**
     * Get the activityCode of the fund request item.
     */
    public function activityCode()
    {
        return $this->belongsTo(ActivityCode::class, 'activity_code_id')->withDefault();
    }

    public function getActivityCode()
    {
        return $this->activityCode->getActivityCodeWithDescription();
    }
}
