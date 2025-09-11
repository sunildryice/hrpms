<?php

namespace Modules\TravelRequest\Models;

use App\Traits\ModelEventLogger;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Master\Models\Status;
use Modules\Privilege\Models\User;

class TravelClaimAttachment extends Model
{
    use HasFactory, ModelEventLogger;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'travel_claim_attachments';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'travel_claim_id',
        'heading',
        'attachment',
        'created_by',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];

    /**
     * Get the travel claim of the log.
     */
    public function travelClaim()
    {
        return $this->belongsTo(TravelClaim::class, 'travel_claim_id');
    }

    /**
     * Get the createdBy of the log.
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'user_id')->withDefault();
    }

    public function getCreatedBy()
    {
        return $this->createdBy->getFullName();
    }
}
