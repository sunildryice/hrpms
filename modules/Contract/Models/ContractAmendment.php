<?php

namespace Modules\Contract\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\ModelEventLogger;
use Illuminate\Support\Str;
use Modules\Employee\Models\Employee;
use Modules\Supplier\Models\Supplier;

class ContractAmendment extends Model
{
    use HasFactory, ModelEventLogger;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'contract_amendments';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'contract_id',
        'amendment_number',
        'contract_date',
        'expiry_date',
        'contract_amount',
        'attachment',
        'remarks',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];

    protected $dates = ['expiry_date'];

    public function contract()
    {
        return $this->belongsTo(Contract::class, 'contract_id')->withDefault();
    }

    public function getExpiryDate()
    {
        return $this->expiry_date->toFormattedDateString();
    }

    public function getShortRemarks()
    {
        return Str::limit($this->remarks, 150);
    }
}
