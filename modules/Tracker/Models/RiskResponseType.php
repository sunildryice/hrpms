<?php

namespace Modules\Tracker\Models;

use Illuminate\Database\Eloquent\Model;

class RiskResponseType extends Model
{
    protected $table = 'lkup_risk_response_types';

    protected $fillable = ['title'];
}
