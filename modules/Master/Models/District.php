<?php

namespace Modules\Master\Models;

use App\Traits\ModelEventLogger;
use Modules\Privilege\Models\User;

use Illuminate\Database\Eloquent\Model;
use Modules\Master\Models\HealthFacility;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class District extends Model
{
    use HasFactory, ModelEventLogger;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'lkup_districts';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'district_name',
        'province_id',
        'enable_field',
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
     * Get the province that owns the district.
     */
    public function province()
    {
        return $this->belongsTo(Province::class, 'province_id')->withDefault();
    }

    /**
     * Get the local levels for the district.
     */
    public function localLevels()
    {
        return $this->hasMany(LocalLevel::class, 'district_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by')->withDefault();
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by')->withDefault();
    }

    public function getCreatedBy()
    {
        return $this->createdBy->getFullName();
    }

    public function healthFacitilies()
    {
        return $this->hasMany(HealthFacility::class, 'district_id');
    }

    public function getDistrictName()
    {
        return $this->district_name;
    }

    public function getEnableField()
    {
        return $this->enable_field ? 'Yes' : 'No';
    }

    public function getProvinceName()
    {
        return $this->province->getProvinceName();
    }

    public function getUpdatedAt()
    {
        return $this->updated_at->toFormattedDateString();
    }
}
