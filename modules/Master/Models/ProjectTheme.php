<?php

namespace Modules\Master\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectTheme extends Model
{
    protected $table = 'lkup_project_themes';

    protected $fillable = [
        'title',
        'enable_field',
        'created_by',
        'updated_by',
    ];
}