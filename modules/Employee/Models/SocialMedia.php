<?php

namespace Modules\Employee\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\ModelEventLogger;

class SocialMedia extends Model
{
    use HasFactory;

    protected $table = 'employee_social_accounts';


    protected $fillable = [
        'employee_id',
        'social_account_id',
        'link',
    ];

    protected $hidden = [];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }
}
