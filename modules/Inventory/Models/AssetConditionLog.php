<?php

namespace Modules\Inventory\Models;

use App\Traits\ModelEventLogger;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Master\Models\Condition;
use Modules\Master\Models\Status;
use Modules\Privilege\Models\User;

class AssetConditionLog extends Model
{
    use HasFactory, ModelEventLogger;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'asset_condition_logs';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'asset_id',
        'condition_id',
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

    /**
     * Get the asset of the log.
     */
    public function asset()
    {
        return $this->belongsTo(Asset::class, 'asset_id')->withDefault();
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by')->withDefault();
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by')->withDefault();
    }

    public function condition()
    {
        return $this->belongsTo(Condition::class, 'condition_id')->withDefault();
    }

    public function getCreatedBy()
    {
        return $this->createdBy->getFullName();
    } 

    public function getDesignation()
    {
        return $this->createdBy->employee->tenures()->where('created_at', '<=', $this->created_at)->latest()->first()->getDesignationName() ?? '';
    }


    public function getUpdatedBy()
    {
        return $this->updatedBy->getFullName();
    }

    public function getCondition()
    {
        return $this->condition->getTitle();
    }

    public function getAssetNumber()
    {
        return $this->asset->getAssetNumber();
    }

    public function getDescription()
    {
        return $this->description;
    }
}
