<?php

namespace Modules\Master\Models;

use App\Traits\ModelEventLogger;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Master\Models\Item;
use Modules\Master\Models\Unit;

class PackageItem extends Model
{
    use HasFactory, ModelEventLogger;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'purchase_request_packages_items';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'package_id',
        'item_id',
        'unit_id',
        'specification',
        'quantity',
        'unit_price',
        'total_price',
        'office_id',
        'account_code_id',
        'donor_code_id',
        'activity_code_id',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];

    public function package()
    {
        return $this->belongsTo(Package::class, 'package_id');
    }

    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id')->withDefault();
    }

    /**
     * Get the unit of the item
     */
    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id')->withDefault();
    }

    public function getItemName()
    {
        return $this->item->getItemName();
    }

    public function getUnitName()
    {
        return $this->unit->getUnitName();
    }
}
