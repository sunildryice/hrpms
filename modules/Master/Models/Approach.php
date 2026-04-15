<?php

namespace Modules\Master\Models;

use Illuminate\Database\Eloquent\Model;

class Approach extends Model
{
    protected $table = 'lkup_approaches';

    protected $fillable = [
        'title',
        'enable_field',
        'created_by',
        'updated_by',
    ];
}