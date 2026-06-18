<?php

namespace Modules\Tracker\Models;

use Illuminate\Database\Eloquent\Model;

class RiskImpact extends Model
{
    protected $table = 'lkup_risk_impacts';

    protected $fillable = ['title'];
}
