<?php

namespace Modules\Master\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\ModelEventLogger;
use Modules\FundRequest\Models\FundRequest;
use Modules\Privilege\Models\User;

class Office extends Model
{
    use HasFactory, ModelEventLogger;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'lkup_offices';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'parent_id',
        'office_type_id',
        'district_id',
        'office_name',
        'office_code',
        'phone_number',
        'fax_number',
        'email_address',
        'account_number',
        'bank_name',
        'branch_name',
        'weekend_type',
        'activated_at',
        'created_by',
        'updated_by',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by')->withDefault();
    }

    /**
     * Get the district that owns the office.
     */
    public function district()
    {
        return $this->belongsTo(District::class, 'district_id')->withDefault();
    }

    /**
     * Get the type of office.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function officeType()
    {
        return $this->belongsTo(OfficeType::class, 'office_type_id')->withDefault();
    }

    /**
     * Get the parent office that owns the office.
     */
    public function parent()
    {
        return $this->belongsTo(Office::class, 'parent_id')->withDefault();
    }

    /**
     * Get all children offices that belong to the office.
     */
    public function childrens()
    {
        return $this->hasMany(Office::class, 'parent_id');
    }

    public function fundRequests()
    {
        return $this->hasMany(FundRequest::class, 'office_id');
    }

    /**
     * Get all holidays that belong to the office.
     */
    public function holidays()
    {
        return $this->belongsToMany(Holiday::class, 'lkup_office_holidays');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by')->withDefault();
    }

    public function getCreatedBy()
    {
        return $this->createdBy->getFullName();
    }

    public function getDistrictName()
    {
        return $this->district->getDistrictName();
    }

    public function getOfficeName()
    {
        return $this->office_name;
    }

    public function getOfficeCode()
    {
        return $this->office_code;
    }

    public function getUpdatedAt()
    {
        return $this->updated_at->toFormattedDateString();
    }

    public function getUpdatedBy()
    {
        return $this->updatedBy->getFullName();
    }

    public function getOfficeType()
    {
        return $this->officeType->getTitle();
    }
}
