<?php

namespace Modules\Master\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Privilege\Models\User;

class ProjectTheme extends Model
{
    protected $table = 'lkup_project_themes';

    protected $fillable = [
        'title',
        'enable_field',
        'created_by',
        'updated_by',
    ];

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