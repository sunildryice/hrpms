<?php

namespace Modules\Master\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use App\Traits\ModelEventLogger;

class DispositionType extends Model
{
    use HasFactory, ModelEventLogger;

    protected $table = 'lkup_disposition_types';

    protected $fillable = [
        'title',
        'created_by',
        'updated_by',
    ];

    public function getDispositionType()
    {
        return strtoupper($this->title);
    }

}
