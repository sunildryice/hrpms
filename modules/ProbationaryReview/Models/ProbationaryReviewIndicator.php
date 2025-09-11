<?php

namespace Modules\ProbationaryReview\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\ModelEventLogger;

use Modules\ProbationaryReview\Models\ProbationaryReview;
use Modules\Master\Models\ProbationaryIndicator;

class ProbationaryReviewIndicator extends Model
{
    use HasFactory, ModelEventLogger;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'probationary_review_indicators';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'probationary_review_id',
        'probationary_indicator_id',
        'improved_required',
        'satisfactory',
        'good',
        'excellent',
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
     * Get the probationary indicator.
     */
    public function probationaryIndicator()
    {
        return $this->belongsTo(ProbationaryIndicator::class, 'probationary_indicator_id');
    }

    public function getIndicator()
    {
        return $this->probationaryIndicator->title;
    }
}
