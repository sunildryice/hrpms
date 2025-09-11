<?php

namespace Modules\Master\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\ModelEventLogger;

class PartnerOrganization extends Model
{
    use HasFactory, ModelEventLogger;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'lkup_partner_organizations';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'district_id',
        'is_active',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];

    public function getUpdatedAt()
    {
        return $this->updated_at->toFormattedDateString();
    }

    public function district()
    {
        return $this->belongsTo(District::class, 'district_id')->withDefault();
    }

    public function getDistrictName()
    {
        return $this->district->getDistrictName();
    }
}
