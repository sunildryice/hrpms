<?php

namespace Modules\Master\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use App\Traits\ModelEventLogger;
use Modules\Privilege\Models\User;

class HealthFacility extends Model
{
    use HasFactory, ModelEventLogger;
    protected $table = 'lkup_health_facilities';

    protected $fillable = [
        'title',
        'province_id',
        'district_id',
        'local_level_id',
        'ward',
        'created_by',
        'updated_by',
        'activated_at'
    ];

    protected $dates = [
        'activated_at'
    ]; 

    public function getTitle()
    {
        return ucfirst($this->title);
    }

    public function province()
    {
        return $this->belongsTo(Province::class, 'province_id')->withDefault();
    }

    public function getProvince()
    {
        return $this->province->getProvinceName();
    }

    public function district()
    {
        return $this->belongsTo(District::class, 'district_id')->withDefault();
    }

    public function getDistrict()
    {
        return $this->district->getDistrictName();
    }

    public function localLevel()
    {
        return $this->belongsTo(LocalLevel::class, 'local_level_id')->withDefault();
    }

    public function getLocalLevel()
    {
        return $this->localLevel->getLocalLevelName();
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by')->withDefault();
    }

    public function getCreatedBy()
    {
        return $this->createdBy->getFullName();
    }

    public function getActivatedAt()
    {
        return $this->activated_at?->toFormattedDateString();
    }

    public function getUpdatedAt()
    {
        return $this->updated_at?->toFormattedDateString();
    }
}