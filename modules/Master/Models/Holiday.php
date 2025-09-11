<?php

namespace Modules\Master\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\ModelEventLogger;
use Modules\Privilege\Models\User;

class Holiday extends Model
{
    use HasFactory, ModelEventLogger;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'lkup_holidays';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'description',
        'holiday_date',
        'is_holiday',
        'only_female',
        'created_by',
        'updated_by',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];

    protected $dates = ['holiday_date'];

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by')->withDefault();
    }

    /**
     * Get all offices that belong to the holiday.
     */
    public function offices()
    {
        return $this->belongsToMany(Office::class, 'lkup_office_holidays');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by')->withDefault();
    }

    public function getCreatedBy()
    {
        return $this->createdBy->getFullName();
    }

    public function getHolidayDate()
    {
        return $this->holiday_date->toFormattedDateString();
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
