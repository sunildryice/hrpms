<?php

namespace Modules\Tracker\Models;

use Illuminate\Database\Eloquent\Model;

class RiskRating extends Model
{
    protected $table = 'lkup_risk_ratings';

    protected $fillable = ['title'];
}
