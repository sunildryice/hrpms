<?php

namespace Modules\TrainingRequest\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\ModelEventLogger;

use Modules\TrainingRequest\Models\TrainingReport;
use Modules\Master\Models\TrainingQuestion;

class TrainingReportQuestion extends Model
{
    use HasFactory, ModelEventLogger;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'training_report_questions';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'training_report_id',
        'question_id',
        'answer',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];

    /**
     * Get the training request.
     */
    public function trainingReport()
    {
        return $this->belongsTo(TrainingReport::class, 'training_report_id');
    }

    /**
     * Get the training question.
     */
    public function trainingQuestion()
    {
        return $this->belongsTo(TrainingQuestion::class, 'question_id');
    }

    public function getQuestion()
    {
        return $this->trainingQuestion->question;
    }
}
