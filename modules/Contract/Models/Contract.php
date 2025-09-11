<?php

namespace Modules\Contract\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\ModelEventLogger;
use Illuminate\Support\Str;
use Modules\Employee\Models\Employee;
use Modules\Supplier\Models\Supplier;

class Contract extends Model
{
    use HasFactory, ModelEventLogger;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'contracts';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'supplier_id',
        'contract_number',
        'description',
        'contact_name',
        'contact_number',
        'address',
        'contract_date',
        'effective_date',
        'expiry_date',
        'reminder_days',
        'termination_days',
        'contract_amount',
        'focal_person_id',
        'attachment',
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

    protected $dates = ['contract_date', 'effective_date', 'expiry_date'];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id')->withDefault();
    }

    public function focalPerson()
    {
        return $this->belongsTo(Employee::class, 'focal_person_id')->withDefault();
    }

    public function amendments()
    {
        return $this->hasMany(ContractAmendment::class, 'contract_id');
    }

    public function latestAmendment()
    {
        return $this->hasOne(ContractAmendment::class, 'contract_id')->latest();
    }

    public function getContractAmount()
    {
        return $this->latestAmendment ? $this->latestAmendment->contract_amount : $this->contract_amount;
    }

    public function getContractDate()
    {
        return $this->contract_date->toFormattedDateString();
    }

    public function getEffectiveDate()
    {
        return $this->effective_date->toFormattedDateString();
    }

    public function getExpiryDate()
    {
        return $this->latestAmendment ? $this->latestAmendment->getExpiryDate() : $this->expiry_date->toFormattedDateString();
    }

    public function getFocalPersonName()
    {
        return $this->focalPerson->full_name;
    }

    public function getSupplierName()
    {
        return $this->supplier->supplier_name;
    }

    public function getShortRemarks()
    {
        return Str::limit($this->remarks, 50);
    }

    public function getVATPANNo()
    {
        return $this->supplier->vat_pan_number;
    }
}
