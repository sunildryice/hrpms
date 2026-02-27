<?php

namespace Modules\Master\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\ModelEventLogger;
use Modules\Privilege\Models\User;

class Brand extends Model
{
    use HasFactory, ModelEventLogger;

    protected $table = 'lkup_brands';

    protected $fillable = [
        'title',
        'description',
        'created_by',
        'updated_by',
    ];

    protected $hidden = [];

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by')->withDefault();
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by')->withDefault();
    }

    // Accessors used in DataTables
    public function getCreatedBy()
    {
        return $this->createdBy->getFullName();
    }

    public function getUpdatedBy()
    {
        return $this->updatedBy->getFullName();
    }

    public function getUpdatedAt()
    {
        return $this->updated_at ? $this->updated_at->toFormattedDateString() : '';
    }
}