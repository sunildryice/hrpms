<?php

namespace Modules\EmployeeExit\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\ModelEventLogger;
use Modules\Master\Models\ExitFeedback;

class ExitInterviewFeedbackAnswer extends Model
{
    use HasFactory, ModelEventLogger;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'exit_interview_feedbacks';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'exit_interview_id',
        'exit_feedback_id',
        'always',
        'almost',
        'usually',
        'sometimes',

    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */

    // protected $dates = ['required_date', 'request_date'];

    public function exitFeedback()
    {
        return $this->belongsTo(ExitFeedback::class,'exit_feedback_id');
    }

    public function exitInterview()
    {
        return $this->belongsTo(ExitInterview::class, 'exit_interview_id');
    }
}
