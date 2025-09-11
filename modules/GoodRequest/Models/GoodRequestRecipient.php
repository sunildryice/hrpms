<?php

namespace Modules\GoodRequest\Models;

use App\Traits\ModelEventLogger;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GoodRequestRecipient extends Model
{
    use HasFactory, ModelEventLogger;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'good_request_recipients';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'good_request_id',
        'name',
        'address',
        'remarks',
        'created_by',
        'updated_by',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];

    protected $dates = [];

    /**
     * Get the good request that owns good request item
     */
    public function goodRequest()
    {
        return $this->belongsTo(GoodRequest::class, 'good_request_id')->withDefault();
    }
}
