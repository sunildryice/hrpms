<?php

namespace Modules\Master\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use App\Traits\ModelEventLogger;
use Modules\Inventory\Models\InventoryItem;
use Modules\Privilege\Models\User;

class Execution extends Model
{
    use HasFactory, ModelEventLogger;
    protected $table = 'lkup_executions';

    protected $fillable = [
        'title',
        'description',
        'created_by',
        'updated_by',
        'activated_at'
    ];

    protected $dates = [
        'activated_at'
    ];

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

    public function getTitle()
    {
        return strtoupper($this->title);
    }

    public function getActivatedAt()
    {
        return $this->activated_at?->toFormattedDateString();
    }

        public function getUpdatedAt()
    {
        return $this->updated_at->toFormattedDateString();
    }

    public function getUpdatedBy()
    {
        return $this->updatedBy->getFullName();
    }

    public function inventoryItems()
    {
        return $this->hasMany(InventoryItem::class, 'execution_id');
    }

}
