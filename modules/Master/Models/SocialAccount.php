<?php

namespace Modules\Master\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SocialAccount extends Model
{
    use HasFactory;

    protected $table = 'lkup_social_accounts';

    protected $fillable = [
        'title',
    ];
}
