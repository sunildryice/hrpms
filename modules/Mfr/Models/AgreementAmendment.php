<?php

namespace Modules\Mfr\Models;

use App\Traits\ModelEventLogger;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Privilege\Models\User;

class AgreementAmendment extends Model
{
    use HasFactory, ModelEventLogger;

    // Database table used by the model.
    protected $table = 'mfr_agreement_amendments';

    // Attributes that are mass assignable.
    protected $fillable = [
        'mfr_agreement_id',
        'effective_date',
        'extension_to_date',
        'approved_budget',
        'attachment',
        'created_by',
    ];

    protected $casts = ['extension_to_date' => 'date:Y-m-d'];

    // Attributes hidden from models JSON or array.
    protected $hidden = [];

    // Turn the columns into carbon object.
    protected $dates = ['effective_date', 'extension_to_date', 'created_at', 'updated_at'];

    public function agreement()
    {
        return $this->belongsTo(Agreement::class, 'mfr_agreement_id')->withDefault();
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by')->withDefault();
    }

    public function getCreatedBy()
    {
        return $this->createdBy->getFullName();
    }

    public function getEffectiveDate()
    {
        return $this->effective_date->toFormattedDateString();
    }

    public function getTotalEstimateCost()
    {
        return $this->total_estimate_cost;
    }

    public function getAttachment()
    {
        return $this->attachment;
    }
}
