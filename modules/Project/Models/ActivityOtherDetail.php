<?php

namespace Modules\Project\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityOtherDetail extends Model
{
    use HasFactory;


    protected $table = 'activity_other_details';

    protected $fillable = [
        'project_activity_id',
        'key',
        'value',
    ];

    public function projectActivity()
    {
        return $this->belongsTo(ProjectActivity::class, 'project_activity_id');
    }
}
