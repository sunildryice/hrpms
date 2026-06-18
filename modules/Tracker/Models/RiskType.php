<?php

namespace Modules\Tracker\Models;

use Illuminate\Database\Eloquent\Model;

class RiskType extends Model
{
    protected $table = 'lkup_risk_types';

    protected $fillable = ['title'];
}
