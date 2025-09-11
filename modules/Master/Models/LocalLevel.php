<?php

namespace Modules\Master\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\ModelEventLogger;
use Modules\Privilege\Models\User;

class LocalLevel extends Model
{
    use HasFactory, ModelEventLogger;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'lkup_local_levels';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'local_level_name',
        'district_id',
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
     * Get district that belong to the local level.
     */
    public function district(){
    	return $this->belongsTo(District::class, 'district_id')->withDefault();
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by')->withDefault();
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by')->withDefault();
    }

    public function healthFacilities()
    {
        return $this->hasMany(HealthFacility::class, 'local_level_id');
    }

    public function getCreatedBy()
    {
        return $this->createdBy->getFullName();
    }

    public function getDistrictName()
    {
        return $this->district->getDistrictName();
    }

    public function getLocalLevelName()
    {
        return $this->local_level_name;
    }

    public function getProvinceName()
    {
        return $this->district->getProvinceName();
    }

    public function getUpdatedAt()
    {
        return $this->updated_at->toFormattedDateString();
    }
}
