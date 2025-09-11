<?php

namespace Modules\Employee\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\ModelEventLogger;
use Modules\Master\Models\BloodGroup;

class MedicalCondition extends Model
{
    use HasFactory, ModelEventLogger;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'employee_medical_condition';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'employee_id ',
        'blood_group_id',
        'medical_condition',
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

    /**
     * Get the employee of the medical condition.
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id ');
    }

    /**
     * Get the blood group of the medical condition.
     */
    public function bloodGroup()
    {
        return $this->belongsTo(BloodGroup::class, 'blood_group_id')->withDefault();
    }
}
