<?php

namespace Modules\Employee\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConsultantLeave extends Model
{
    protected $table = 'consultant_leaves';

    protected $fillable = [
        'employee_id',
        'earn_leave',
        'leave_percentage',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'earn_leave'       => 'boolean',
        'leave_percentage' => 'integer',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}