<?php

namespace Modules\Master\Models;

use Illuminate\Database\Eloquent\Model;

class Sector extends Model
{
    protected $table = 'lkup_sectors';

    protected $fillable = [
        'title',
        'enable_field',
        'created_by',
        'updated_by',
    ];
}