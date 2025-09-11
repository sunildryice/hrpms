<?php

namespace Modules\Lta\Models;

use Illuminate\Support\Str;
use App\Traits\ModelEventLogger;
use Modules\Employee\Models\Employee;
use Modules\Supplier\Models\Supplier;
use Illuminate\Database\Eloquent\Model;
use Modules\PurchaseOrder\Models\PurchaseOrder;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LtaContract extends Model
{
    use HasFactory, ModelEventLogger;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'lta_contracts';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'supplier_id',
        'office_id',
        'contract_date',
        'start_date',
        'end_date',
        'expiry_date',
        'prefix',
        'contract_number',
        'description',
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

    protected $dates = ['contract_date', 'start_date', 'end_date'];

    public function ltaItems()
    {
        return $this->hasMany(LtaContractItem::class, 'lta_contract_id');
    }

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

    public function purchaseOrder()
    {
        return $this->hasMany(PurchaseOrder::class, 'lta_contract_id');
    }

    public function getContractDate()
    {
        return $this->contract_date->toFormattedDateString();
    }

    public function getStartDate()
    {
        return $this->start_date->toFormattedDateString();
    }

    public function getEndDate()
    {
        return $this->end_date->toFormattedDateString();
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
