<?php

namespace Modules\TrainingRequest\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\ModelEventLogger;

use Modules\TrainingRequest\Models\TrainingRequest;
use Modules\Master\Models\TrainingQuestion;

class TrainingRequestQuestion extends Model
{
    use HasFactory, ModelEventLogger;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'training_questions';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'training_id',
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
    public function trainingRequest()
    {
        return $this->belongsTo(TrainingRequest::class, 'training_id');
    }

    /**
     * Get the training question.
     */
    public function trainingQuestion()
    {
        return $this->belongsTo(TrainingQuestion::class, 'question_id')->withDefault();
    }

    public function getQuestion()
    {
        return $this->trainingQuestion->question;
    }
}
