<?php

namespace Modules\ConstructionTrack\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\ModelEventLogger;
use Modules\Privilege\Models\User;

class ConstructionAmendment extends Model
{
    use HasFactory, ModelEventLogger;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'construction_amendments';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'construction_id',
        'effective_date',
        'extension_to_date',
        'total_estimate_cost',
        'attachment',
        'created_by'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'effective_date',
        'extension_to_date'
    ];

    public function construction()
    {
        return $this->belongsTo(Construction::class, 'construction_id')->withDefault();
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by')->withDefault();
    }

    public function getCreatedBy()
    {
        return $this->createdBy->getFullName();
    }

    public function getEffectiveDate()
    {
        return $this->effective_date->toFormattedDateString();
    }

    public function getTotalEstimateCost()
    {
        return $this->total_estimate_cost;
    }

    public function getAttachment()
    {
        return $this->attachment;
    }

    public function getExtensionToDate()
    {
        return $this->extension_to_date ? $this->extension_to_date->format('M d, Y') : '';
    }


}
