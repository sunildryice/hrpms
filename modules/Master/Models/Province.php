<?php

namespace Modules\Master\Models;

use App\Traits\ModelEventLogger;
use Modules\Privilege\Models\User;

use Illuminate\Database\Eloquent\Model;
use Modules\Master\Models\HealthFacility;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Province extends Model
{
    use HasFactory, ModelEventLogger;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'lkup_provinces';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'province_name',
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
     * Get the districts for the province.
     */
    public function districts()
    {
        return $this->hasMany(District::class, 'province_id');
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

    public function getProvinceName()
    {
        return $this->province_name;
    }

    public function healthFacilities()
    {
        return $this->hasMany(HealthFacility::class, 'province_id');
    }

    public function getUpdatedAt()
    {
        return $this->updated_at->toFormattedDateString();
    }
}
