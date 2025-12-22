<?php

namespace Modules\Master\Models;

use App\Traits\ModelEventLogger;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Privilege\Models\User;
use Modules\WorkFromHome\Models\WorkFromHome;

class ProjectCode extends Model
{
    use HasFactory, ModelEventLogger;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'lkup_project_codes';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'short_name',
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

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by')->withDefault();
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by')->withDefault();
    }

    public function getShortName()
    {
        return $this->short_name ?: preg_replace('/\b(\w)|./', '$1', $this->title);
    }

    public function getCreatedBy()
    {
        return $this->createdBy->getFullName();
    }

    public function getProjectCode()
    {
        return $this->title;
    }

    public function getProjectCodeWithDescription()
    {
        return $this->title . ' : ' . $this->description;
    }

    public function getUpdatedAt()
    {
        return $this->updated_at->toFormattedDateString();
    }

    public function getUpdatedBy()
    {
        return $this->updatedBy->getFullName();
    }

    public function projects()
    {
        return $this->belongsToMany(WorkFromHome::class, 'project_work_from_home')
            ->withPivot('deliverables')
            ->withTimestamps();
    }
}
