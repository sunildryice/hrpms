<?php

namespace Modules\PerformanceReview\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Modules\PerformanceReview\Models\PerformanceReview;

use App\Traits\ModelEventLogger;
use Modules\Master\Models\Status;
use Modules\Privilege\Models\User;

class PerformanceReviewLog extends Model
{
    use HasFactory, ModelEventLogger;

    protected $table = 'performance_review_logs';

    protected $fillable = [
        'performance_review_id',
        'user_id',
        'original_user_id',
        'log_remarks',
        'status_id'
    ];

    protected $hidden = [];

    protected $dates = [];

    public function performanceReview()
    {
        return $this->belongsTo(PerformanceReview::class, 'performance_review_id')->withDefault();
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'user_id')->withDefault();
    }

    public function status()
    {
        return $this->belongsTo(Status::class, 'status_id');
    }
}