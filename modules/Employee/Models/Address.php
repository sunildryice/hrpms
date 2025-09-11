<?php

namespace Modules\Employee\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\ModelEventLogger;
use Modules\Employee\Models\Employee;
use Modules\Master\Models\Province;
use Modules\Master\Models\District;
use Modules\Master\Models\LocalLevel;
// use Modules\Configuration\Models\Department;
// use Modules\Configuration\Models\Designation;
// use Modules\Configuration\Models\Office;

class Address extends Model
{
    use HasFactory, ModelEventLogger;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'employee_address';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'employee_id',
        'permanent_province_id',
        'permanent_district_id',
        'permanent_local_level_id',
        'permanent_ward',
        'permanent_tole',
        'temporary_province_id',
        'temporary_district_id',
        'temporary_local_level_id',
        'temporary_ward',
        'temporary_tole',
        'current_location',
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
     * Get the employee of the address.
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function permanent_province()
    {
        return $this->belongsTo(Province::class, 'permanent_province_id')->withDefault();
    }

    public function permanent_district()
    {
        return $this->belongsTo(District::class, 'permanent_district_id')->withDefault();
    }

    public function permanent_local_level()
    {
        return $this->belongsTo(LocalLevel::class, 'permanent_local_level_id')->withDefault();
    }

    public function temporary_province()
    {
        return $this->belongsTo(Province::class, 'temporary_province_id')->withDefault();
    }

    public function temporary_district()
    {
        return $this->belongsTo(District::class, 'temporary_district_id')->withDefault();
    }

    public function temporary_local_level()
    {
        return $this->belongsTo(LocalLevel::class, 'temporary_local_level_id')->withDefault();
    }

    public function getPermanentAddress()
    {
        $address = '';

        if ($this->permanent_province?->province_name) {
            $address .= $this->permanent_province->province_name;
        }

        if ($this->permanent_district?->district_name) {
            if (empty($address)) {
                $address .= $this->permanent_district->district_name;
            } else {
                $address .= ', '.$this->permanent_district->district_name;
            }
        }

        if ($this->permanent_local_level?->local_level_name) {
            if (empty($address)) {
                $address .= $this->permanent_local_level->local_level_name;
            } else {
                $address .= ', '.$this->permanent_local_level->local_level_name;
            }
        }

        return $address;
    }

    public function getTemporaryAddress()
    {
        $address = '';

        if ($this->temporary_province?->province_name) {
            $address .= $this->temporary_province->province_name;
        }

        if ($this->temporary_district?->district_name) {
            if (empty($address)) {
                $address .= $this->temporary_district->district_name;
            } else {
                $address .= ', '.$this->temporary_district->district_name;
            }
        }

        if ($this->temporary_local_level?->local_level_name) {
            if (empty($address)) {
                $address .= $this->temporary_local_level->local_level_name;
            } else {
                $address .= ', '.$this->temporary_local_level->local_level_name;
            }
        }

        return $address;
    }
}
