<?php

namespace Modules\Master\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\ModelEventLogger;
use Modules\Inventory\Models\InventoryItem;
use Modules\Privilege\Models\User;

class Item extends Model
{
    use HasFactory, ModelEventLogger;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'lkup_items';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'inventory_category_id',
        'title',
        'item_code',
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

    public function category()
    {
        return $this->belongsTo(InventoryCategory::class, 'inventory_category_id')->withDefault();
    }

    public function inventoryItems()
    {
        return $this->hasMany(InventoryItem::class, 'item_id');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by')->withDefault();
    }

    /**
     * Get all units that belong to the item.
     */
    public function units()
    {
        return $this->belongsToMany(Unit::class, 'lkup_item_units', 'item_id', 'unit_id');
    }

    public function getCategory()
    {
        return $this->category->title;
    }

    public function getCreatedBy()
    {
        return $this->createdBy->getFullName();
    }

    public function getItemName()
    {
        return $this->title .' - '. $this->item_code;
    }

    public function getUpdatedAt()
    {
        return $this->updated_at->toFormattedDateString();
    }

    public function getUpdatedBy()
    {
        return $this->updatedBy->getFullName();
    }
}
