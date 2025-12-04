<?php

namespace Modules\Master\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\ModelEventLogger;
use Modules\Privilege\Models\User;

class ActivityCode extends Model
{
    use HasFactory, ModelEventLogger;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'lkup_activity_codes';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'description',
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

    protected $dates = ['activated_at'];

    /**
     * Get all account code that belong to the activity code.
     */
    public function accountCodes()
    {
        return $this->belongsToMany(AccountCode::class, 'lkup_activity_account_codes', 'activity_code_id', 'account_code_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by')->withDefault();
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by')->withDefault();
    }

    public function getCreatedBy()
    {
        return $this->createdBy->getFullName();
    }

    public function getActivityCode()
    {
        return $this->title;
    }

    public function getActivityCodeWithDescription()
    {
        return $this->title .' : '. $this->description;
    }

    public function getActivityCodeDescription()
    {
        return $this->description;
    }

    public function getUpdatedAt()
    {
        return $this->updated_at->toFormattedDateString();
    }

    public function getUpdatedBy()
    {
        return $this->updatedBy->getFullName();
    }
}
