<?php

namespace Modules\TrainingRequest\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\ModelEventLogger;

use Modules\Master\Models\Status;
use Modules\Privilege\Models\User;

use Modules\TrainingRequest\Models\TrainingRequest;
use Modules\TrainingRequest\Models\TrainingReportLog;
use Modules\TrainingRequest\Models\TrainingReportQuestion;

class TrainingReport extends Model
{
    use HasFactory, ModelEventLogger;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'training_reports';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'training_id',
        'reviewer_id',
        'approver_id',
        'remarks',
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
    protected $dates = ['start_date', 'end_date'];


    /**
     * Get the approver.
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approver_id')->withDefault();
    }

    /**
     * Get the reporter
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by')->withDefault();
    }

     /**
     * Get the logs for the training request.
     */
    public function logs()
    {
        return $this->hasMany(TrainingReportLog::class, 'training_report_id');
    }

    /**
     * Get the reviewer.
     */
    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id')->withDefault();
    }

    /**
     * Get the report status.
     */
    public function status()
    {
        return $this->belongsTo(Status::class, 'status_id')->withDefault();
    }

    /**
     * Get the training request.
     */
    public function trainingRequest()
    {
        return $this->belongsTo(TrainingRequest::class, 'training_id');
    }

    /**
     * Get the training report question.
     */
    public function trainingReportQuestion()
    {
        return $this->hasMany(TrainingReportQuestion::class, 'training_report_id');
    }

    public function getApproverName()
    {
        return $this->approver->getFullName();
    }

    public function getCreatedBy()
    {
        return $this->createdBy->getFullName();
    }

    public function getEndDate()
    {
        return $this->end_date->toFormattedDateString();
    }

    public function getReviewerName()
    {
        return $this->reviewer->getFullName();
    }

    public function getStartDate()
    {
        return $this->start_date->toFormattedDateString();
    }


    public function getStatus()
    {
        return ucwords($this->status->title);
    }

    public function getStatusClass()
    {
        return $this->status->status_class;
    }
}
