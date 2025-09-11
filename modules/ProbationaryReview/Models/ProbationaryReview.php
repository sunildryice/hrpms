<?php

namespace Modules\ProbationaryReview\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\ModelEventLogger;

use Modules\Employee\Models\Employee;
use Modules\ProbationaryReview\Models\ProbationaryReviewIndicator;
use Modules\ProbationaryReview\Models\ProbationaryReviewLog;
use Modules\ProbationaryReview\Models\ProbationaryReviewQuestion;
use Modules\Master\Models\ProbationaryReviewType;
use Modules\Master\Models\Status;
use Modules\Privilege\Models\User;

class ProbationaryReview extends Model
{
    use HasFactory, ModelEventLogger;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'probationary_reviews';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'review_id',
        'employee_id',
        'date',
        'remarks',
        'performance_improvements',
        'concern_address_summary',
        'employee_performance_progress',
        'objectives_met',
        'objectives_review_remarks',
        'objectives_review_date',
        'development_addressed',
        'development_review_remarks',
        'development_review_date',
        'supervisor_recommendation',
        'director_recommendation',
        'appointment_confirmed',
        'reason_to_address_difficulty',
        'employee_remarks',
        'probation_extended',
        'reason_and_improvement_to_extend',
        'next_probation_complete_date',
        'extension_length',
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
    protected $dates = ['date', 'next_probation_complete_date'];


    /**
     * Get the approver.
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approver_id');
    }
    /**
    * Get the createdBy
    */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by')->withDefault();
    }


    /**
     * Get the employee.
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    /**
     * Get the logs for the probation review.
     */
    public function logs()
    {
        return $this->hasMany(ProbationaryReviewLog::class, 'p_review_id');
    }

    public function approvedLog()
    {
        return $this->hasOne(ProbationaryReviewLog::class, 'p_review_id')
            ->where('status_id', config('constant.APPROVED_STATUS'))
            ->latest();
    }

    /**
     * Get the review type.
     */
    public function reviewType()
    {
        return $this->belongsTo(ProbationaryReviewType::class, 'review_id');
    }

    /**
     * Get the probationary review indicator.
     */
    public function probationaryReviewIndicator()
    {
        return $this->hasMany(ProbationaryReviewIndicator::class, 'probationary_review_id');
    }

    /**
     * Get the probationary review questions.
     */
    public function probationaryReviewQuestion()
    {
        return $this->hasMany(ProbationaryReviewQuestion::class, 'probationary_review_id');
    }

     /**
     * Get the reviewer.
     */
    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

     /**
     * Get the probationary review status.
     */
    public function status()
    {
        return $this->belongsTo(Status::class, 'status_id')->withDefault();
    }

    public function getApproverName()
    {
        return $this->approver->getFullName();
    }

    public function getEmployeeName()
    {
        return $this->employee->full_name;
    }

    public function getProbationEndDate()
    {
        return $this->next_probation_complete_date->toFormattedDateString();
    }

    public function getReviewerName()
    {
        return $this->reviewer->getFullName();
    }

    public function getReviewDate()
    {
        return $this->date->toFormattedDateString();
    }

    public function getReviewType()
    {
        return $this->reviewType->title;
    }

    public function getStatus()
    {
        return $this->status->title;
    }

    public function getStatusClass()
    {
        return $this->status->status_class;
    }



}
