<?php

namespace Modules\Employee\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\ModelEventLogger;
use Modules\Master\Models\BloodGroup;
use Modules\Master\Models\District;
use Modules\Master\Models\FamilyRelation;
use Modules\Master\Models\LocalLevel;
use Modules\Master\Models\Province;

class FamilyDetail extends Model
{
    use HasFactory, ModelEventLogger;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'employee_family_details';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'employee_id',
        'family_relation_id',
        'full_name',
        'date_of_birth',
        'emergency_contact_at',
        'nominee_at',
        'province_id',
        'district_id',
        'local_level_id',
        'ward',
        'tole',
        'remarks',
        'contact_number',
        'created_by',
        'updated_by',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];

    protected $dates = ['date_of_birth', 'emergency_contact_at', 'nominee_at'];

    /**
     * Get the employee of the family detail.
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    /**
     * Get the relation of the family detail.
     */
    public function familyRelation()
    {
        return $this->belongsTo(FamilyRelation::class, 'family_relation_id')->withDefault();
    }

    /**
     * Get the province of the family detail.
     */
    public function province()
    {
        return $this->belongsTo(Province::class, 'province_id')->withDefault();
    }

    /**
     * Get the district of the family detail.
     */
    public function district()
    {
        return $this->belongsTo(District::class, 'district_id')->withDefault();
    }

    /**
     * Get the localLevel of the family detail.
     */
    public function localLevel()
    {
        return $this->belongsTo(LocalLevel::class, 'local_level_id')->withDefault();
    }

    public function getFullName()
    {
        return $this->full_name;
    }

    public function getRelationName()
    {
        return $this->familyRelation->title;
    }

    public function getDateOfBirth()
    {
        return $this->date_of_birth ? $this->date_of_birth->toFormattedDateString() : "";
    }

    public function getAge()
    {
        $years = '';
        if($this->date_of_birth)
        {
            $currentDate = date("Y-m-d");
            $diff = abs(strtotime($currentDate) - strtotime($this->date_of_birth));
            $years = floor($diff / (365*60*60*24));
        }
        return $years;
    }

    public function getAddress()
    {
        $address = '';

        if ($this->province->province_name) {
            $address .= $this->province->province_name;
        }

        if ($this->district->district_name) {
            if (empty($address)) {
                $address .= $this->district->district_name;
            } else {
                $address .= ', '.$this->district->district_name;
            }
        }

        if ($this->localLevel->local_level_name) {
            if (empty($address)) {
                $address .= $this->localLevel->local_level_name;
            } else {
                $address .= ', '.$this->localLevel->local_level_name;
            }
        }
        
        return $address;
    }

}
