<?php

namespace Modules\AdvanceRequest\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\ModelEventLogger;
use Modules\Master\Models\District;
use Modules\Master\Models\FiscalYear;
use Modules\Master\Models\ActivityCode;
use Modules\Master\Models\AccountCode;
use Modules\Master\Models\DonorCode;
use Modules\Master\Models\Office;
use Modules\Master\Models\Status;
use Modules\Privilege\Models\User;

class SettlementActivity extends Model
{
    use HasFactory, ModelEventLogger;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'advance_settlement_activities';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'advance_settlement_id',
        'description',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];

   /**
     * Get the advance order of the advance order item.
     */
    public function advancesettlement()
    {
        return $this->belongsTo(Settlement::class, 'advance_settlement_id');
    }


}
