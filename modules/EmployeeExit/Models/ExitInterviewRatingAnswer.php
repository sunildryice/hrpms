<?php

namespace Modules\EmployeeExit\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\ModelEventLogger;
use Modules\Master\Models\ExitRating;

class ExitInterviewRatingAnswer extends Model
{
    use HasFactory, ModelEventLogger;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'exit_interview_ratings';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'exit_interview_id',
        'exit_rating_id',
        'excellent',
        'good',
        'fair',
        'poor',

    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */

    public function exitRating()
    {
        return $this->belongsTo(ExitRating::class,'exit_rating_id');
    }
    public function exitInterview()
    {
        return $this->belongsTo(ExitInterview::class, 'exit_interview_id')->withDefault();
    }

}
