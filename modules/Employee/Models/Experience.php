<?php

namespace Modules\Employee\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\ModelEventLogger;

class Experience extends Model
{
    use HasFactory, ModelEventLogger;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'employee_experiences';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'employee_id',
        'institution',
        'position',
        'period_from',
        'period_to',
        'attachment',
        'remarks',
        'created_by',
        'updated_by',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];

    protected $dates = ['period_from', 'period_to'];

    /**
     * Get the employee of the family detail.
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function getPeriodFrom()
    {
        return $this->period_from ? $this->period_from->toFormattedDateString() : "";
    }

    public function getPeriodTo()
    {
        return $this->period_to ? $this->period_to->toFormattedDateString() : "";
    }
}
