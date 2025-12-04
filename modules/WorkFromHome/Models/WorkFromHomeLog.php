<?php

namespace Modules\WorkFromHome\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Privilege\Models\User;

class WorkFromHomeLog extends Model
{
    use HasFactory;

    protected $table = 'work_from_home_logs';

    protected $fillable = [
        'work_from_home_id',
        'user_id',
        'log_remarks',
        'status_id',
        'original_user_id',
    ];

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
