<?php

namespace Modules\Master\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\ModelEventLogger;
use Modules\Privilege\Models\User;

class LeaveType extends Model
{
    use HasFactory, ModelEventLogger;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'lkup_leave_types';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'fiscal_year_id',
        'title',
        'short_description',
        'leave_frequency',
        'number_of_days',
        'leave_basis',
        'maximum_carry_over',
        'paid',
        'default',
        'male',
        'include_weekends',
        'female',
        'applicable_to_all',
        'encashment',
        'activated_at',
        'created_by',
        'updated_by',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];

    protected $casts = [
        'number_of_days' => 'decimal:1'
    ];

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by')->withDefault();
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by')->withDefault();
    }

    public function getActiveStatus()
    {
        return $this->activated_at ? 'Active' : 'Inactive';
    }

    public function getApplicableToAllStatus()
    {
        return $this->applicable_to_all == 1 ? 'Yes' : 'No';
    }

    public function getCreatedBy()
    {
        return $this->createdBy->getFullName();
    }

    public function getEncashmentStatus()
    {
        return $this->encashment == 1 ? 'Yes' : 'No';
    }

    public function getFemaleStatus()
    {
        return $this->female == 1 ? 'Yes' : 'No';
    }

    public function getIncludeWeekendsStatus()
    {
        return $this->include_weekends == 1 ? 'Yes' : 'No';
    }

    public function getLeaveBasis()
    {
        return $this->leave_basis == 2 ? 'Hour' : 'Day';
    }

    public function getLeaveFrequency()
    {
        return $this->leave_frequency == 3 ? 'Event Based' : ($this->leave_frequency == 2 ? 'Monthly' : 'Yearly');
    }

    public function getLeaveName()
    {
        return $this->title;
    }

    public function getMaleStatus()
    {
        return $this->male == 1 ? 'Yes' : 'No';
    }

    public function getPaidStatus()
    {
        return $this->paid == 1 ? 'Paid' : 'Unpaid';
    }

    public function getUpdatedAt()
    {
        return $this->updated_at ? $this->updated_at->toFormattedDateString() : '';
    }

    public function getUpdatedBy()
    {
        return $this->updatedBy->getFullName();
    }
}
