<?php

namespace Modules\Master\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use App\Traits\ModelEventLogger;

class Condition extends Model
{
    use HasFactory, ModelEventLogger;

    protected $table = 'lkup_conditions';

    protected $fillable = [
        'title',
        'created_by',
        'updated_by',
        'activated_at'
    ];

    protected $dates = [
        'activated_at'
    ];

    public function getTitle()
    {
        return strtoupper($this->title);
    }

    public function getActivatedAt()
    {
        return $this->activated_at?->toFormattedDateString();
    }
}
