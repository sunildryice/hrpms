<?php

namespace Modules\ProbationaryReview\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\ModelEventLogger;

use Modules\ProbationaryReview\Models\ProbationaryReview;
use Modules\Master\Models\ProbationaryQuestion;

class ProbationaryReviewQuestion extends Model
{
    use HasFactory, ModelEventLogger;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'probationary_review_questions';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'probationary_review_id',
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
     * Get the probationary review.
     */
    public function probationaryReview()
    {
        return $this->belongsTo(ProbationaryReview::class, 'probationary_review_id');
    }

    /**
     * Get the probationary question.
     */
    public function probationaryQuestion()
    {
        return $this->belongsTo(ProbationaryQuestion::class, 'question_id');
    }

    public function getQuestion()
    {
        return $this->probationaryQuestion->question;
    }
}
