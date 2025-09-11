<?php

namespace Modules\TrainingRequest\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\ModelEventLogger;

use Modules\Master\Models\AccountCode;
use Modules\Master\Models\ActivityCode;
use Modules\Master\Models\FiscalYear;
use Modules\Master\Models\Status;
use Modules\TrainingRequest\Models\TrainingReport;
use Modules\TrainingRequest\Models\TrainingRequestLog;
use Modules\TrainingRequest\Models\TrainingRequestQuestion;
use Modules\Privilege\Models\User;

class TrainingRequest extends Model
{
    use HasFactory, ModelEventLogger;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'trainings';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'reviewer_id',
        'recommender_id',
        'approver_id',
        'activity_code_id',
        'account_code_id',
        'fiscal_year_id',
        'prefix',
        'training_number',
        'title',
        'start_date',
        'end_date',
        'own_time',
        'work_time',
        'duration',
        'course_fee',
        'approved_amount',
        'description',
        'attachment',
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
    // protected $dates = ['created_at'];



    /**
     * Get the approver.
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approver_id')->withDefault();
    }

     /**
     * Get the createdBy of a training request
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by')->withDefault();
    }

    /**
     * Get the Account Code.
     */
    public function accountCode()
    {
        return $this->belongsTo(AccountCode::class, 'account_code_id')->withDefault();
    }

    /**
     * Get the Activity Code.
     */
    public function activityCode()
    {
        return $this->belongsTo(ActivityCode::class, 'activity_code_id')->withDefault();
    }

    /**
     * Get the fiscal year.
     */
    public function fiscalYear()
    {
        return $this->belongsTo(Fiscalyear::class, 'fiscal_year_id')->withDefault();
    }

     /**
     * Get the logs for the training request.
     */
    public function logs()
    {
        return $this->hasMany(TrainingRequestLog::class, 'training_id');
    }

    public function submittedLog()
    {
        return $this->hasOne(TrainingRequestLog::class, 'training_id')->where('status_id', config('constant.SUBMITTED_STATUS'))->latest()->withDefault();
    }

    public function approvedLog()
    {
        return $this->hasOne(TrainingRequestLog::class, 'training_id')->where('status_id', config('constant.APPROVED_STATUS'))->latest()->withDefault();
    }

    public function recommendedLog()
    {
        return $this->hasOne(TrainingRequestLog::class, 'training_id')->where('status_id', config('constant.RECOMMENDED2_STATUS'))->latest()->withDefault();
    }

    /**
     * Get the training report
     */
    public function trainingReport()
    {
        return $this->hasOne(TrainingReport::class, 'training_id');
    }

    /**
     * Get the training request question.
     */
    public function trainingRequestQuestion()
    {
        return $this->hasMany(TrainingRequestQuestion::class, 'training_id');
    }

    /**
     * Get the recommender of a training request
     */
    public function recommender()
    {
        return $this->belongsTo(User::class, 'recommender_id')->withDefault();
    }

    /**
     * Get the requester of a training request
     */
    public function requester()
    {
        return $this->belongsTo(User::class, 'created_by')->withDefault();
    }

    /**
     * Get the reviewer.
     */
    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id')->withDefault();
    }

    /**
     * Get the training request status.
     */
    public function status()
    {
        return $this->belongsTo(Status::class, 'status_id')->withDefault();
    }

    public function getApproverName()
    {
        return $this->approver->getFullName();
    }

    public function getRecommenderName()
    {
        return $this->recommender->getFullName();
    }

    public function getRequesterName()
    {
        return $this->requester->getFullName();
    }

    public function getReviewerName()
    {
        return $this->reviewer->getFullName();
    }


    public function getCreatedBy()
    {
        return $this->createdBy->getFullName();
    }

    public function getCreatedAt()
    {
        return $this->created_at->toFormattedDateString();
    }


    public function getDuration()
    {
        return $this->getStartDate().' - '. $this->getEndDate();
    }

    public function getEndDate()
    {
        return $this->end_date->toFormattedDateString();
    }

    public function getAccountCode()
    {
        return $this->accountCode->getAccountCodeWithDescription();
    }

    public function getActivityCode()
    {
        return $this->activityCode->getActivityCodeWithDescription();
    }

    public function getStartDate()
    {
        return $this->start_date->toFormattedDateString();
    }

    public function getStatus()
    {
        return $this->status->title;
    }

    public function getStatusClass()
    {
        return $this->status->status_class;
    }

    /**
     * Get the total days of the travel.
     */
    public function getTotalDays()
    {
        return $this->end_date ? $this->end_date->diffInDays($this->start_date) : 1;
    }

    public function getTrainingRequestNumber()
    {
        $fiscalYear = $this->fiscalYear ? '/'.substr($this->fiscalYear->title, 2): '';
        return $this->prefix .'-'. $this->training_number . $fiscalYear;
    }

    public function getTrainingRequestApprovedDate()
    {
        $record = $this->hasOne(TrainingRequestLog::class, 'training_id')
                    ->latest()->first();

        return $record?->status_id == 6 ? $record->created_at->format('M d, Y') : '';
    }

    public function getTrainingReportSubmissionStatus()
    {
        return $this->trainingReport?->status->title == 'submitted' ? 'Submitted' : 'Pending';
    }
}
