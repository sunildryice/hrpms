<?php

namespace Modules\TravelRequest\Models;

use App\Traits\ModelEventLogger;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use Modules\Attachment\Models\Attachment;
use Modules\Master\Models\Status;
use Modules\Privilege\Models\User;

class TravelReport extends Model
{
    use HasFactory, ModelEventLogger;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'travel_reports';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'travel_request_id',
        'objectives',
        'major_achievement',
        'not_completed_activities',
        'conclusion_recommendations',
        'total_travel_days',
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

    /**
     * Get the approver of the travel report
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approver_id')->withDefault();
    }

    /**
     * Get all the Travel Report's attachments
     */
    public function attachments(): MorphMany
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }

    /**
     * Get the logs for the travel report.
     */
    public function logs()
    {
        return $this->hasMany(TravelReportLog::class, 'travel_report_id')
            ->orderBy('created_at', 'desc');
    }

    /**
     * Get the reporter
     */
    public function reporter()
    {
        return $this->belongsTo(User::class, 'created_by')->withDefault();
    }

    /**
     * Get the report status.
     */
    public function status()
    {
        return $this->belongsTo(Status::class, 'status_id')->withDefault();
    }

    /**
     * Get the travel request of the travel report.
     */
    public function travelRequest()
    {
        return $this->belongsTo(TravelRequest::class, 'travel_request_id');
    }

    /**
     * Get the recommendation of the travel report.
     */
    public function travelReportRecommendations()
    {
        return $this->hasMany(TravelReportRecommendation::class, 'travel_report_id');
    }

    public function getApproverName()
    {
        return $this->approver->getFullName();
    }

    public function getReporterName()
    {
        return $this->reporter->getFullName();
    }

    public function getStatus()
    {
        return ucwords($this->status->title);
    }

    public function getStatusClass()
    {
        return $this->status->status_class;
    }

    public function returnLog()
    {
        return $this->hasOne(TravelReportLog::class, 'travel_report_id')
            ->whereStatusId(config('constant.RETURNED_STATUS'))
            ->latest()->withDefault();
    }

}
