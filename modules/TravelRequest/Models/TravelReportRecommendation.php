<?php

namespace Modules\TravelRequest\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\ModelEventLogger;

use Modules\TravelRequest\Models\TravelReport;

class TravelReportRecommendation extends Model
{
    use HasFactory, ModelEventLogger;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'travel_report_recommendations';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'travel_report_id',
        'recommendation_subject',
        'recommendation_date',
        'recommendation_responsible',
        'recommendation_remarks',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];


    /**
     * Get the office of the employee.
     */
    public function travelReport()
    {
        return $this->belongsTo(TravelReport::class, 'travel_report_id');
    }
}
