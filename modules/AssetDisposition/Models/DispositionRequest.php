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
use Modules\AssetDisposition\Models\DispositionRequestLog;



class DispositionRequest extends Model
{
    use HasFactory, ModelEventLogger;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'disposition_requests';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'disposition_type_id',
        'disposition_date',
        'requester_id',
        'reviewer_id',
        'approver_id',
        'status_id',
        'office_id',
        'created_by',
        'updated_by',
     ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];

    protected $dates = ['disposition_date'];

    protected $casts = [
        'disposition_date' => 'date:Y-m-d',
    ];

    public function disposeAssets()
    {
        return $this->hasMany(DispositionRequestAsset::class, 'disposition_request_id');
    }

    public function office()
    {
        return $this->belongsTo(Office::class, 'office_id')->withDefault();
    }

    public function dispositionType()
    {
        return $this->belongsTo(DispositionType::class, 'disposition_type_id')->withDefault();
    }

    public function status(){
        return $this->belongsTo(Status::class,'status_id')->withDefault();
    }

    public function getDispositionType()
    {
        return $this->dispositionType->title;
    }

    public function getStatusClass(){
        return $this->status->status_class;
    }

    public function getStatus(){
        return $this->status->title;
    }

    public function getDispositionDate(){
        return $this->disposition_date->format('M d, Y');
    }

    /**
     * Get the logs for the asset disposition.
     */
    public function logs(){
        return $this->hasMany(DispositionRequestLog::class,'disposition_request_id')
        ->orderBy('created_at','desc');
    }

    /**
     * Get the submitted log for the asset dispositionl.
     */
    public function submittedLog()
    {
        return $this->hasOne(DispositionRequestLog::class, 'disposition_request_id')
            ->whereStatusId(config('constant.SUBMITTED_STATUS'))
            ->latest();
    }

    /**
     * Get the approved log for the asset dispositionl.
     */
    public function approvedLog()
    {
        return $this->hasOne(DispositionRequestLog::class, 'disposition_request_id')
            ->whereStatusId(config('constant.APPROVED_STATUS'))
            ->latest();
    }

    public function returnedLog()
    {
        return $this->hasOne(DispositionRequestLog::class, 'disposition_request_id')
            ->where('status_id', config('constant.RETURNED_STATUS'))
            ->latest()->withDefault();
    }

    /**
     * Get the approver
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approver_id')->withDefault();
    }

    public function getApproverName(){
        return $this->approver->getFullName();
    }

    public function attachments():MorphMany
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }
    /**
     * Get the requester of the asset dispositionl
     */
    public function requester()
    {
        return $this->belongsTo(User::class, 'requester_id')->withDefault();
    }

    public function getRequesterName(){
        return $this->requester->getFullName();
    }


    public function getOfficeName()
    {
        return $this->office->office_name;
    }

    public function getDisposedAssetCodes() {
        $assetsCodes = [];
        foreach($this->disposeAssets as $asset) {
            $assetsCodes[] = $asset->asset->asset_code;
        }

        return $assetsCodes;

    }
}
