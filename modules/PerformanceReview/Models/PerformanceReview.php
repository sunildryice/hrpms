<?php

namespace Modules\PerformanceReview\Models;

use App\Traits\ModelEventLogger;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Employee\Models\Employee;
use Modules\Master\Models\FiscalYear;
use Modules\Master\Models\Status;
use Modules\Privilege\Models\User;

class PerformanceReview extends Model
{
    use HasFactory, ModelEventLogger;

    protected $table = 'performance_reviews';

    protected $fillable = [
        'employee_id',
        'fiscal_year_id',
        'review_type_id',
        'review_from',
        'review_to',
        'deadline_date',
        'status_id',
        'requester_id',
        'reviewer_id',
        'recommender_id',
        'approver_id',
        'goal_setting_date',
        'mid_term_per_date',
        'final_per_date',
        'created_by',
        'updated_by',
    ];

    protected $hidden = [];

    protected $dates = [
        'review_from',
        'review_to',
        'deadline_date',
        'goal_setting_date',
        'mid_term_per_date',
        'final_per_date',
    ];

    public function answers()
    {
        return $this->hasMany(PerformanceReviewAnswer::class, 'performance_review_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approver_id')->withDefault();
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id')->withDefault();
    }

    public function fiscalYear()
    {
        return $this->belongsTo(FiscalYear::class, 'fiscal_year_id')->withDefault();
    }

    public function keyGoals()
    {
        return $this->hasMany(PerformanceReviewKeyGoal::class, 'performance_review_id');
    }

    public function developmentPlans()
    {
        return $this->hasMany(PerformanceProfessionalDevelopmentPlan::class, 'performance_review_id');
    }

    public function logs()
    {
        return $this->hasMany(PerformanceReviewLog::class, 'performance_review_id');
    }

    public function reviewType()
    {
        return $this->belongsTo(PerformanceReviewType::class, 'review_type_id')->withDefault();
    }

    public function requester()
    {
        return $this->belongsTo(User::class, 'requester_id')->withDefault();
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id')->withDefault();
    }

    public function recommender()
    {
        return $this->belongsTo(User::class, 'recommender_id')->withDefault();
    }

    public function status()
    {
        return $this->belongsTo(Status::class, 'status_id')->withDefault();
    }

    public function getStatus()
    {
        return ucwords($this->status->title);
    }

    public function getStatusClass()
    {
        return $this->status->status_class;
    }

    public function getEmployeeName()
    {
        return $this->employee->getFullName();
    }

    public function getEmployeeTitle()
    {
        return $this->employee->latestTenure->getDesignationName();
    }

    public function getSupervisorName()
    {
        return $this->employee->latestTenure->getSupervisorName();
    }

    public function getSupervisorTitle()
    {
        return $this->employee->latestTenure->getSupervisorDesignation();
    }

    public function getTechnicalSupervisorName()
    {
        return $this->employee->latestTenure->getCrossSupervisorName();
    }

    public function getTechnicalSupervisorTitle()
    {
        return $this->employee->latestTenure->getCrossSupervisorDesignation();
    }

    public function getJoinedDate()
    {
        return $this->employee->latestTenure->getJoinedDate();
    }

    public function getKeyGoalCurrent()
    {
        if ($this->review_type_id == 1 || $this->review_type_id == 2) {
            $keyGoalReview = PerformanceReview::where('employee_id', $this->employee_id)
                ->where('review_type_id', 3)
                ->where('fiscal_year_id', $this->fiscal_year_id)
                ->first();

            return $keyGoalReview?->keyGoals()
                ->where('type', 'current')->get()->concat($this->keyGoals()->where('type', 'current')->get());
        } elseif ($this->review_type_id == 3) {
            return $this->keyGoals()->where('type', 'current')->get();
        }

        return null;
    }

    // public function getProfessionalDevelopmentPlan()
    // {
    //     return $this->getAnswer(10);
    // }

    public function getProfessionalDevelopmentPlan()
    {
        return $this->developmentPlans;
    }

    public function getKeyGoalsFields($value = 1)
    {
        $keyGoals = $this->getKeyGoalCurrent();
        if ($keyGoals) {
            $titles = $keyGoals->map(function ($goal, $index) use ($value) {
                $returnString = match ($value) {
                    1 => ++$index . '. ' . $goal->title,
                    2 => ++$index . '. ' . $goal->description_employee_annual,
                    3 => ++$index . '. ' . $goal->description_supervisor_annual,
                };

                return $returnString;
            })->toArray();

            return implode("\n", $titles);
        }
    }

    public function getReviewType()
    {
        return $this->reviewType->title;
    }

    public function getReviewFromDate()
    {
        return $this->review_from->format('M d, Y');
    }

    public function getReviewToDate()
    {
        return $this->review_to->format('M d, Y');
    }

    public function getAnswer($questionId)
    {
        return $this->answers->where('question_id', $questionId)->first()?->answer;
    }

    public function getAnswerShort($questionId)
    {
        return substr($this->answers->where('question_id', $questionId)->first()?->answer, 0, 100);
    }

    public function getPerformanceEvalAnswer()
    {
        $rateMap = [1 => 5, 2 => 4, 3 => 3, 4 => 2, 5 => 1];
        $answers = $this->answers()->with('performanceReviewQuestion')->whereBetween('question_id', [11, 15])->get();
        foreach ($answers as $answer) {
            if ($answer->answer == 'true') {
                return $answer->performanceReviewQuestion->question . '-' . $rateMap[$answer->performanceReviewQuestion->position];
            }
        }

        return null;
    }

    public function getKeyGoals()
    {
        return $this->keyGoals()->get();
    }

    public function getLatestRemark()
    {
        return $this->logs->last()->log_remarks;
    }

    public function getDutyStation()
    {
        return $this->employee->latestTenure->getDutyStation();
    }

    public function getGoalSettingDate()
    {
        $date = '';
        if ($this->review_type_id == 3) {
            $log = $this->logs->last();

            if ($log) {
                $date = $log->status_id == config('constant.APPROVED_STATUS') ? $log->created_at->toFormattedDateString() : '';
            } else {
                $date = '';
            }

            return $date;
        }

        return $date;
    }

    public function getMidTermPerDate()
    {
        $date = '';
        if ($this->review_type_id == 2) {
            $log = $this->logs->last();

            if ($log) {
                $date = $log->status_id == config('constant.APPROVED_STATUS') ? $log->created_at->toFormattedDateString() : '';
            } else {
                $date = '';
            }

            return $date;
        }

        return $date;
    }

    public function getFinalPerDate()
    {
        $date = '';
        if ($this->review_type_id == 1) {
            $log = $this->logs->last();

            if ($log) {
                $date = $log->status_id == config('constant.APPROVED_STATUS') ? $log->created_at->toFormattedDateString() : '';
            } else {
                $date = '';
            }

            return $date;
        }

        return $date;
    }

    public function getFiscalYear()
    {
        return $this->fiscalYear->getFiscalYear();
    }

    public function getRecommenderName()
    {
        return $this->recommender->getFullName();
    }

    public function getApproverName()
    {
        return $this->approver->getFullName();
    }

    public function getDeadlineDate()
    {
        return $this->deadline_date?->toFormattedDateString();
    }

    public function midtermReviewRequired()
    {
        return !$this->employee->firstTenure->joined_date->isCurrentYear();
    }
}
