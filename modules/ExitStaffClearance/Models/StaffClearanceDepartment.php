<?php

namespace Modules\ExitStaffClearance\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Modules\ExitStaffClearance\Models\StaffClearanceRecord;

use App\Traits\ModelEventLogger;

class StaffClearanceDepartment extends Model
{
    use HasFactory, ModelEventLogger;

    protected $table = 'lkup_staff_clearance_departments';

    protected $fillable = [
        'parent_id',
        'title',
        'activated_at',
    ];

    protected $hidden = [];

    protected $dates = ['activated_at'];

    public function parent()
    {
        return $this->belongsTo(StaffClearanceDepartment::class, 'parent_id')->withDefault();
    }

    public function childrens()
    {
        return $this->hasMany(StaffClearanceDepartment::class, 'parent_id');
    }

    public function getParent()
    {
        return $this->parent->title;
    }

    public function records()
    {
        return $this->hasMany(StaffClearanceRecord::class, 'clearance_department_id');
    }
}
