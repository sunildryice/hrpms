<?php

namespace Modules\Master\Models;

use App\Traits\ModelEventLogger;
use Modules\Privilege\Models\User;
use Modules\Master\Models\PackageItem;
use Illuminate\Database\Eloquent\Model;
use Modules\PurchaseRequest\Models\PurchaseRequestItem;

class Package extends Model
{
    use ModelEventLogger;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'purchase_request_packages';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'package_name',
        'package_description',
        'total_amount',
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

    public function packageItems()
    {
        return $this->hasMany(PackageItem::class, 'package_id');
    }

    public function purchaseRequestItems()
    {
        return $this->hasMany(PurchaseRequestItem::class, 'package_id');
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

    public function getUpdatedAt()
    {
        return $this->updated_at->toFormattedDateString();
    }

    public function getUpdatedBy()
    {
        return $this->updatedBy->getFullName();
    }

    public function getTotalAmount()
    {
        return number_format($this->total_amount, 2);
    }

    public function getItemCount()
    {
        return $this->packageItems()->count();
    }

}
