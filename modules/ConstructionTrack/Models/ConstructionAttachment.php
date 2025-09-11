<?php

namespace Modules\ConstructionTrack\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\ModelEventLogger;
use Modules\Privilege\Models\User;

class ConstructionAttachment extends Model
{
    use HasFactory, ModelEventLogger;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'construction_attachments';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'construction_id',
        'link',
        'title',
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
    protected $dates = [];

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

    public function getTitle()
    {
        return ucfirst($this->title);
    }

    public function getAttachment()
    {
        return $this->attachment;
    }


}
