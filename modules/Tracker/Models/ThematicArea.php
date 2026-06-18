<?php

namespace Modules\Tracker\Models;

use Illuminate\Database\Eloquent\Model;

class ThematicArea extends Model
{
    protected $table = 'lkup_thematic_areas';

    protected $fillable = ['title'];
}
