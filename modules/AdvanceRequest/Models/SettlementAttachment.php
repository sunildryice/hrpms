<?php

namespace Modules\AdvanceRequest\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\ModelEventLogger;
use Modules\AdvanceRequest\Models\Settlement;
use Modules\Privilege\Models\User;

class SettlementAttachment extends Model
{
    use HasFactory, ModelEventLogger;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'advance_settlement_attachments';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'advance_settlement_id',
        'title', 
        'attachment',
        'link',
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
    protected $dates = [];

    public function settlement()
    {
        return $this->belongsTo(Settlement::class, 'advance_settlement_id')->withDefault();
    }
    
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by')->withDefault();
    }

    public function getCreatedBy()
    {
        return $this->createdBy->getFullName();
    }

    public function getTitle()
    {
        return ucfirst($this->title);
    }

    public function getAttachment()
    {
        return $this->attachment;
    }


}