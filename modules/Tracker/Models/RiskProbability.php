<?php

namespace Modules\Tracker\Models;

use Illuminate\Database\Eloquent\Model;

class RiskProbability extends Model
{
    protected $table = 'lkup_risk_probabilitis';

    protected $fillable = ['title'];
}
