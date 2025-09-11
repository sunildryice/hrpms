<?php

namespace Modules\Master\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\ModelEventLogger;
use Modules\Privilege\Models\User;

class InventoryCategory extends Model
{
    use HasFactory, ModelEventLogger;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'lkup_inventory_categories';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'inventory_type_id',
        'title',
        'description',
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

    public function inventoryType()
    {
        return $this->belongsTo(InventoryType::class, 'inventory_type_id')->withDefault();
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by')->withDefault();
    }

    public function getCreatedBy()
    {
        return $this->createdBy->getFullName();
    }

    public function getInventoryType()
    {
        return $this->inventoryType->title;
    }

    public function getUpdatedAt()
    {
        return $this->updated_at ? $this->updated_at->toFormattedDateString() : '';
    }

    public function getUpdatedBy()
    {
        return $this->updatedBy->getFullName();
    }
}
