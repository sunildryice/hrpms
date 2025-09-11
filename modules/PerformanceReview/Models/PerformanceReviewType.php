<?php

namespace Modules\PerformanceReview\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\ModelEventLogger;

class PerformanceReviewType extends Model
{
    use HasFactory, ModelEventLogger;

    protected $table = 'lkup_performance_review_types';

    protected $fillable = [
        'title'
    ];

    protected $hidden = [];

    protected $dates = [];

    public function performanceReviews()
    {
        return $this->hasMany(PerformanceReview::class, 'review_type_id');
    }
}