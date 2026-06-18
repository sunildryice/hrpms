<?php

namespace Modules\Tracker\Models;

use Illuminate\Database\Eloquent\Model;

class RiskStatus extends Model
{
    protected $table = 'lkup_risk_status';

    protected $fillable = ['title'];
}
