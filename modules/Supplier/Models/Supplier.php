<?php

namespace Modules\Supplier\Models;

use App\Traits\ModelEventLogger;
use Modules\Privilege\Models\User;

use Modules\Lta\Models\LtaContract;
use Illuminate\Database\Eloquent\Model;
use Modules\PurchaseOrder\Models\PurchaseOrder;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Supplier extends Model
{
    use HasFactory, ModelEventLogger;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'suppliers';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'supplier_type',
        'supplier_name',
        'address1',
        'address2',
        'contact_number',
        'email_address',
        'contact_person_name',
        'contact_person_email_address',
        'vat_pan_number',
        'account_number',
        'account_name',
        'bank_name',
        'branch_name',
        'swift_code',
        'remarks',
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

    public function purchaseOrders()
    {
        return $this->hasMany(PurchaseOrder::class, 'supplier_id');
    }

    public function ltaContracts()
    {
        return $this->hasMany(LtaContract::class, 'supplier_id');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by')->withDefault();
    }

    public function getCreatedBy()
    {
        return $this->createdBy->getFullName();
    }

    public function getSupplierName()
    {
        return $this->supplier_name;
    }

    public function getSupplierNameandVAT()
    {
        return $this->supplier_name.'-'.$this->vat_pan_number;
    }


    public function getUpdatedAt()
    {
        return $this->updated_at->toFormattedDateString();
    }

    public function getUpdatedBy()
    {
        return $this->updatedBy->getFullName();
    }

    public function getSupplierType()
    {
        return $this->supplier_type == 1 ? 'Organization' : 'Individual';
    }
}
