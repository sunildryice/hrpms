<?php

namespace Modules\ExitStaffClearance\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Modules\ExitStaffClearance\Models\StaffClearance;
use Modules\ExitStaffClearance\Models\StaffClearanceDepartment;

use App\Traits\ModelEventLogger;
use Modules\Privilege\Models\User;

class StaffClearanceRecord extends Model
{
    use HasFactory, ModelEventLogger;

    protected $table = 'staff_clearance_records';

    protected $fillable = [
        'staff_clearance_id',
        'clearance_department_id',
        'employee_id',
        'cleared_at',
        'remarks',
        'created_by',
        'updated_by'
    ];

    protected $hidden = [];

    protected $dates = ['cleared_at'];

    public function performanceReview()
    {
        return $this->belongsTo(StaffClearance::class, 'staff_clearance_id')->withDefault();
    }

    public function clearedBy()
    {
        return $this->belongsTo(User::class, 'created_by')->withDefault();
    }

    public function clearanceDepartment()
    {
        return $this->belongsTo(StaffClearanceDepartment::class, 'clearance_department_id');
    }

    public function getClearedDate()
    {
        return $this->cleared_at?->format('Y-m-d');
    }

    public function getClearedByName()
    {
        return $this->clearedBy->getFullName();
    }
}
