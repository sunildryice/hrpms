<?php

namespace Modules\WorkFromHome\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkFromHomeDay extends Model
{
    use HasFactory;

    protected $table = 'work_from_home_days';

    protected $fillable = [
        'work_from_home_id',
        'date',
        'type',
    ];

    public function workFromHome()
    {
        return $this->belongsTo(WorkFromHome::class, 'work_from_home_id');
    }

    public function getFormattedDateAttribute()
    {
        return $this->date ? Carbon::parse($this->date)->format('M d, Y') : null;
    }
}
