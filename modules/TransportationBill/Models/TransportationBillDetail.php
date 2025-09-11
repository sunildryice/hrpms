<?php

namespace Modules\TransportationBill\Models;

use App\Traits\ModelEventLogger;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Master\Models\AccountCode;
use Modules\Master\Models\ActivityCode;
use Modules\Master\Models\DonorCode;
use Modules\Master\Models\Item;
use Modules\Master\Models\Unit;
use Modules\Privilege\Models\User;
use Modules\FundOrder\Models\FundOrderItem;

class TransportationBillDetail extends Model
{
    use HasFactory, ModelEventLogger;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'transportation_bill_details';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'transportation_bill_id',
        'quantity',
        'item_description',
        'remarks',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];

    /**
     * Get the transportation bill of the transportation bill detail.
     */
    public function transportationBill()
    {
        return $this->belongsTo(TransportationBill::class, 'transportation_bill_id');
    }
}
