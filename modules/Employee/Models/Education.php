<?php

namespace Modules\Employee\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\ModelEventLogger;
use Modules\Master\Models\EducationLevel;
use Modules\Employee\Models\Employee;

class Education extends Model
{
    use HasFactory, ModelEventLogger;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'employee_educations';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'employee_id',
        'education_level_id',
        'institution',
        'degree',
        'passed_year',
        'attachment',
        'created_by',
        'updated_by',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id ');
    }

     /**
     * Get the education level.
     */
    public function educationLevel()
    {
        return $this->belongsTo(EducationLevel::class, 'education_level_id')->withDefault();
    }

    public function getEducationLevel()
    {
        return $this->educationLevel->title;
    }

    public function getDegree()
    {
        return $this->degree;
    }

    public function getInstitution()
    {
        return $this->institution;
    }

    public function getPassedYear()
    {
        return $this->passed_year;
    }
}
