<?php

namespace Modules\ConstructionTrack\Models;

use App\Traits\ModelEventLogger;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Privilege\Models\User;
use Modules\ConstructionTrack\Models\ConstructionProgress;

class ConstructionProgressAttachment extends Model
{
    use HasFactory, ModelEventLogger;

    protected $table = 'construction_progress_attachments';

    protected $fillable = [
        'construction_progress_id',
        'title',
        'attachment',
        'created_by'
    ];

    protected $hidden = [];

    public function constructionProgress()
    {
        return $this->belongsTo(ConstructionProgress::class, 'construction_progress_id')->withDefault();
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by')->withDefault();
    }
}
