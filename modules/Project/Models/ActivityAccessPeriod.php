<?php

namespace Modules\Project\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class ActivityAccessPeriod extends Model
{
    protected $table = 'lkup_activity_access_periods';

    protected $fillable = [
        'start_date',
        'end_date',
        'is_active',
    ];

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}
