<?php

namespace Modules\Master\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\ModelEventLogger;

class OfficeType extends Model
{
    use HasFactory, ModelEventLogger;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'lkup_office_types';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'created_by',
        'updated_by'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];

    /**
     * Get the offices that belong to the office type.
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function offices(){
    	return $this->hasMany(Office::class, 'office_type_id');
    }

    public function getTitle()
    {
        return ucfirst($this->title);
    }

    public function getUpdatedAt()
    {
        return $this->updated_at->toFormattedDateString();
    }
}
