<?php

namespace Modules\AssetDisposition\Models;

use App\Traits\ModelEventLogger;
use Modules\Master\Models\Office;
use Modules\Master\Models\Status;

use Modules\Privilege\Models\User;
use Modules\Inventory\Models\Asset;
use Illuminate\Database\Eloquent\Model;
use Modules\Attachment\Models\Attachment;
use Modules\Master\Models\DispositionType;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;



class DispositionRequestAsset extends Model
{
    use HasFactory, ModelEventLogger;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'disposition_request_assets';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'asset_id',
        'disposition_request_id',
        'disposition_reason',
        'created_by',
        'updated_by',
     ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];

    public function asset()
    {
        return $this->belongsTo(Asset::class, 'asset_id');
    }

    public function dispositionRequest()
    {
        return $this->belongsTo(DispositionRequest::class, 'disposition_request_id');
    }

    public function getDispositionType()
    {
        return $this->dispositionRequest->dispositionType->title;
    }

   
    public function getDispositionDate(){
        return $this->dispositionRequest->disposition_date->format('d-m-Y');
    }

    public function getAssetCode()
    {
        return $this->asset->prefix . '-' . $this->asset->asset_number;
    }

    public function getAssetNumber()
    {
        return $this->asset->getAssetNumber();
    }

    public function getOfficeName()
    {
        return $this->dispositionRequest->office->getOfficeName();
    }
   
}
