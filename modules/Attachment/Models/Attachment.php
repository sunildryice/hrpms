<?php

namespace Modules\Attachment\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\ModelEventLogger;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Modules\Privilege\Models\User;

class Attachment extends Model
{
    use HasFactory, ModelEventLogger;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'attachments';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'attachable_id',
        'attachable_type',
        'title',
        'attachment',
        'link',
        'created_by',
        'updated_by'
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

    /**
     * Get the parent attachable model
     */
    public function attachable(): MorphTo
    {
        return $this->morphTo();
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
        return $this->createdBy?->getFullName();
    }
    
    public function getUpdatedBy()
    {
        return $this->updatedBy?->getFullName();
    }

    public function getTitle()
    {
        return $this->title;
    }
}